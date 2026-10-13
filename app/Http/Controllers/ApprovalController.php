<?php

namespace App\Http\Controllers;

use App\Models\TravelRequest;
use App\Models\User;
use App\Notifications\TripDecisionNotification;
use App\Notifications\TripSubmittedNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ApprovalController extends Controller
{
    /**
     * Display the Approvals & Decision Center queue.
     *
     * Supports filtering by all pending requests, flagged policy exceptions,
     * and high-value requisitions exceeding Rp 10.000.000.
     */
    public function index(Request $request): View|JsonResponse
    {
        $filter = $request->query('filter', 'all');

        // Base query for pending authorization items
        $baseQuery = TravelRequest::with('traveler')
            ->where(function ($q) {
                $q->where('approval_stage', 'like', '%Pending%')
                    ->orWhere('approval_stage', 'like', '%Review%')
                    ->orWhere('approval_stage', 'like', '%Manager%')
                    ->orWhere('approval_stage', 'like', '%Director%');
            });

        // Compute queue counters for top indicator tabs
        $pendingApprovalsCount = (clone $baseQuery)->count();
        $flaggedCount = (clone $baseQuery)->where('policy_status', '!=', 'compliant')->count();
        $tier1Count = (clone $baseQuery)->where('total_cost', '<=', 5000000)->count();
        $tier2Count = (clone $baseQuery)->where('total_cost', '>', 5000000)->where('total_cost', '<=', 15000000)->count();
        $tier3Count = (clone $baseQuery)->where('total_cost', '>', 15000000)->count();
        $highValueCount = $tier3Count;

        // Apply active filter
        $query = clone $baseQuery;

        if ($filter === 'flagged') {
            $query->where('policy_status', '!=', 'compliant');
        } elseif ($filter === 'tier1') {
            $query->where('total_cost', '<=', 5000000);
        } elseif ($filter === 'tier2') {
            $query->where('total_cost', '>', 5000000)->where('total_cost', '<=', 15000000);
        } elseif ($filter === 'tier3' || $filter === 'high_value') {
            $query->where('total_cost', '>', 15000000);
        }

        $pendingRequests = $query->latest()->paginate(15);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'filter' => $filter,
                'counts' => [
                    'pending' => $pendingApprovalsCount,
                    'flagged' => $flaggedCount,
                    'tier1' => $tier1Count,
                    'tier2' => $tier2Count,
                    'tier3' => $tier3Count,
                    'high_value' => $highValueCount,
                ],
                'data' => $pendingRequests,
            ]);
        }

        return view('approvals.index', compact(
            'pendingRequests',
            'pendingApprovalsCount',
            'flaggedCount',
            'tier1Count',
            'tier2Count',
            'tier3Count',
            'highValueCount',
            'filter'
        ));
    }

    /**
     * Display the specified travel authorization for approval review.
     */
    public function show(string $id): View
    {
        $trip = TravelRequest::with('traveler')
            ->where('request_code', $id)
            ->orWhere('id', $id)
            ->firstOrFail();

        return view('trips.show', [
            'trip' => $trip,
            'isApprovalView' => true,
        ]);
    }

    /**
     * Record an executive authorization decision (Authorize, Decline, or Request Clarification).
     * Enforces the Waterfall Account Limit authorization matrix.
     */
    public function recordDecision(Request $request, string $id): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'action' => 'required|string|in:approve,reject,clarification,request_clarification',
            'reason' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:1000',
        ]);

        $trip = TravelRequest::with('traveler')
            ->where('request_code', $id)
            ->orWhere('id', $id)
            ->firstOrFail();
        $decision = $validated['action'];
        $notes = $validated['reason'] ?? $validated['notes'] ?? null;
        $user = auth()->user();

        // 1. Enforce Waterfall Account Authorization check on approval
        if ($user && $decision === 'approve') {
            $authCheck = $user->canAuthorizeTrip($trip);
            if (! $authCheck['allowed']) {
                if ($request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $authCheck['reason'],
                    ], 403);
                }

                return back()->with('error', $authCheck['reason']);
            }
        }

        // 2. Process Decision according to Waterfall Account Limit Matrix
        if ($decision === 'approve') {
            $waterfall = $trip->determineNextApprovalStage();
            $trip->approval_stage = $waterfall['next_stage'];
            $trip->overall_status = $waterfall['overall_status'];

            if ($waterfall['is_final']) {
                $trip->stage_step = $trip->total_steps;
            } else {
                $trip->stage_step = min($trip->total_steps, (int) $trip->stage_step + 1);
            }

            $message = $waterfall['message'];
        } elseif ($decision === 'reject') {
            $trip->approval_stage = 'Ditolak';
            $trip->overall_status = 'cancelled';
            $message = "Pengajuan perjalanan #{$trip->request_code} telah ditolak.".($notes ? " Alasan: {$notes}" : '');
        } else {
            $trip->approval_stage = 'Klarifikasi Diajukan';
            $message = "Permintaan klarifikasi telah dikirimkan ke pemohon terkait Pengajuan #{$trip->request_code}.".($notes ? " Catatan: {$notes}" : '');
        }

        $trip->save();

        // 3. Record in approval_steps audit trail
        if ($user) {
            DB::table('approval_steps')->updateOrInsert(
                [
                    'travel_request_id' => $trip->id,
                    'step_order' => $trip->stage_step,
                ],
                [
                    'step_type' => str_contains($trip->approval_stage, 'Finance') ? 'finance' : 'manager',
                    'approver_id' => $user->id,
                    'status' => $decision === 'approve' ? 'approved' : 'rejected',
                    'comment' => $notes,
                    'acted_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        // 4. Role-based notification: Notify employee about decision
        if ($trip->traveler) {
            $trip->traveler->notify(new TripDecisionNotification($trip, $decision, $notes));
        }

        // 5. Role-based escalation notification based on Waterfall Stage
        if ($decision === 'approve') {
            if (str_contains($trip->approval_stage, 'Finance')) {
                // Escalate to Finance Approvers
                $financeApprovers = User::where('role', 'like', '%Finance%')
                    ->orWhere('department', 'like', '%Finance%')
                    ->orWhere('band', 'Band 5')
                    ->get();
                foreach ($financeApprovers as $fa) {
                    if ($fa->id !== auth()->id()) {
                        $fa->notify(new TripSubmittedNotification($trip));
                    }
                }
            } elseif (str_contains($trip->approval_stage, 'Director')) {
                // Escalate to C-Suite / Executive Directors
                $directors = User::where('role', 'like', '%Director%')
                    ->orWhere('role', 'like', '%C-Suite%')
                    ->orWhere('band', 'Band 5')
                    ->get();
                foreach ($directors as $dir) {
                    if ($dir->id !== auth()->id()) {
                        $dir->notify(new TripSubmittedNotification($trip));
                    }
                }
            }
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'trip' => $trip,
            ]);
        }

        return redirect()->route('approvals.index')
            ->with('success', $message);
    }

    /**
     * Perform batch authorization on standard, policy-compliant travel requests.
     */
    public function bulkApprove(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'trip_ids' => 'required|array|min:1',
            'trip_ids.*' => 'required|string',
        ]);

        $updatedCount = TravelRequest::whereIn('id', $validated['trip_ids'])
            ->where('policy_status', 'compliant')
            ->update(['approval_stage' => 'Finance Review']);

        $message = "Successfully bulk-approved {$updatedCount} compliant travel requests.";

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'count' => $updatedCount,
                'message' => $message,
            ]);
        }

        return redirect()->route('approvals.index')->with('success', $message);
    }
}
