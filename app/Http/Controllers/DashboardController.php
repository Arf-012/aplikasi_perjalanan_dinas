<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use App\Models\TravelRequest;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the executive travel command center dashboard.
     *
     * Aggregates active authorizations, pending approval queues,
     * departmental budget encumbrances, and policy compliance rates.
     */
    public function index(Request $request): View|JsonResponse
    {
        // 1. Core Executive KPIs
        $totalTripsCount = TravelRequest::count();

        $activeTripsCount = TravelRequest::where(function ($query) {
            $query->where('approval_status', 'like', '%Issued%')
                  ->orWhere('approval_status', 'like', '%Confirmed%')
                  ->orWhere('approval_status', 'like', '%Line Manager%')
                  ->orWhere('approval_status', 'like', '%Finance%')
                  ->orWhere('approval_status', 'like', '%Director%');
        })->count();

        $pendingApprovalsCount = TravelRequest::where(function ($query) {
            $query->where('approval_status', 'like', '%Pending%')
                  ->orWhere('approval_status', 'like', '%Review%')
                  ->orWhere('approval_status', 'like', '%Manager%');
        })->count();

        // 2. Budget & Policy Compliance Calculations
        $totalCommittedCost = TravelRequest::where('approval_status', '!=', 'Rejected')->sum('estimated_cost');

        $compliantCount = TravelRequest::where('policy_status', 'compliant')->count();
        $policyComplianceRate = $totalTripsCount > 0
            ? round(($compliantCount / $totalTripsCount) * 100, 1)
            : 98.4;

        // 3. Spotlight Trip (Active Authorization Highlight)
        $spotlightTrip = TravelRequest::with('traveler')
            ->where('approval_status', 'like', '%Ticket Issued%')
            ->orWhere('approval_status', 'like', '%Confirmed%')
            ->latest()
            ->first() ?? TravelRequest::with('traveler')->latest()->first();

        // 4. Recent Travel Requests Queue
        $recentTrips = TravelRequest::with('traveler')
            ->latest()
            ->take(8)
            ->get();

        // Support AJAX/JSON API consumption if requested
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'metrics' => [
                    'active_trips'           => $activeTripsCount,
                    'pending_approvals'      => $pendingApprovalsCount,
                    'total_trips'            => $totalTripsCount,
                    'total_committed_cost'   => $totalCommittedCost,
                    'policy_compliance_rate' => $policyComplianceRate,
                ],
                'spotlight_trip' => $spotlightTrip,
                'recent_trips'   => $recentTrips,
            ]);
        }

        return view('dashboard', compact(
            'activeTripsCount',
            'pendingApprovalsCount',
            'totalTripsCount',
            'totalCommittedCost',
            'policyComplianceRate',
            'spotlightTrip',
            'recentTrips'
        ));
    }

    /**
     * Return live KPI metrics for dashboard polling or asynchronous refresh.
     */
    public function metrics(): JsonResponse
    {
        $activeTrips = TravelRequest::whereIn('approval_status', [
            'Ticket Issued / Confirmed',
            'Manager Review',
            'Finance Review'
        ])->count();

        $pendingApprovals = TravelRequest::where('approval_status', 'like', '%Pending%')
            ->orWhere('approval_status', 'like', '%Review%')
            ->count();

        $quarterlyBudgetUsed = TravelRequest::sum('estimated_cost');

        return response()->json([
            'status' => 'success',
            'timestamp' => now()->toIso8601String(),
            'data' => [
                'active_authorizations' => $activeTrips,
                'pending_decisions'     => $pendingApprovals,
                'budget_encumbered_idr' => $quarterlyBudgetUsed,
                'sla_alert_count'       => TravelRequest::where('policy_status', 'flagged')->count(),
            ],
        ]);
    }
}
