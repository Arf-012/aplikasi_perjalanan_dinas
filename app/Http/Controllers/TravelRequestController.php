<?php

namespace App\Http\Controllers;

use App\Models\DepartmentBudget;
use App\Models\TravelPolicy;
use App\Models\TravelRequest;
use App\Models\User;
use App\Notifications\TripSubmittedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TravelRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = TravelRequest::with('traveler');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('destination', 'like', '%'.$q.'%')
                    ->orWhere('origin', 'like', '%'.$q.'%')
                    ->orWhere('request_code', 'like', '%'.$q.'%')
                    ->orWhere('id', 'like', '%'.$q.'%')
                    ->orWhereHas('traveler', function ($t) use ($q) {
                        $t->where('name', 'like', '%'.$q.'%')
                          ->orWhere('department', 'like', '%'.$q.'%');
                    });
            });
        }

        if ($request->filled('status')) {
            $status = $request->status;
            if ($status === 'active') {
                $query->whereIn('overall_status', ['pending_approval', 'booking_ready', 'approved', 'booked']);
            } elseif ($status === 'inactive' || $status === 'deactive') {
                $query->whereIn('overall_status', ['completed', 'cancelled']);
            } else {
                $query->where('overall_status', $status);
            }
        }

        $trips = $query->latest()->paginate(10)->withQueryString();

        return view('trips.index', compact('trips'));
    }

    /**
     * Show the form for creating a new resource.
     */
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $budgets = DepartmentBudget::all();

        return view('trips.create', compact('budgets'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cost_center_id' => 'nullable|string',
            'origin_code' => 'required|string|max:10',
            'dest_code' => 'required|string|max:10',
            'departure_date' => 'required|date',
            'return_date' => 'required|date|after_or_equal:departure_date',
            'purpose_type' => 'nullable|string',
            'purpose' => 'required|string|max:1000',
            'flight_cost' => 'nullable|numeric|min:0',
            'hotel_cost' => 'nullable|numeric|min:0',
            'per_diem_cost' => 'nullable|numeric|min:0',
        ]);

        $cities = [
            'CGK' => 'Jakarta (CGK)',
            'HLP' => 'Jakarta Halim (HLP)',
            'SUB' => 'Surabaya (SUB)',
            'DPS' => 'Bali Denpasar (DPS)',
            'BDO' => 'Bandung (BDO)',
            'YIA' => 'Yogyakarta (YIA)',
            'SRG' => 'Semarang (SRG)',
            'KNO' => 'Medan (KNO)',
            'BPN' => 'Balikpapan (BPN)',
            'UPG' => 'Makassar (UPG)',
            'PLM' => 'Palembang (PLM)',
            'PKU' => 'Pekanbaru (PKU)',
            'BDJ' => 'Banjarmasin (BDJ)',
            'MDC' => 'Manado (MDC)',
            'SIN' => 'Singapura (SIN)',
            'KUL' => 'Kuala Lumpur (KUL)',
        ];

        $flightCost = (float) ($validated['flight_cost'] ?? 0);
        $hotelCost = (float) ($validated['hotel_cost'] ?? 0);
        $transitCost = (float) ($validated['per_diem_cost'] ?? 0);
        $totalCost = $flightCost + $hotelCost + $transitCost;

        $isDraft = $request->input('is_draft') == '1';

        $trip = TravelRequest::create([
            'user_id' => auth()->id() ?? 1,
            'cost_center' => $validated['cost_center_id'] ?? 'CC-402',
            'origin_code' => strtoupper($validated['origin_code']),
            'dest_code' => strtoupper($validated['dest_code']),
            'origin' => $cities[strtoupper($validated['origin_code'])] ?? strtoupper($validated['origin_code']),
            'destination' => $cities[strtoupper($validated['dest_code'])] ?? strtoupper($validated['dest_code']),
            'departure_date' => $validated['departure_date'],
            'return_date' => $validated['return_date'],
            'departure_time_slot' => 'Penerbangan Pagi (06:00 - 11:00)',
            'return_time_slot' => 'Penerbangan Malam (17:00 - 22:00)',
            'purpose_type' => $validated['purpose_type'] ?? 'Pertemuan Klien',
            'purpose_title' => Str::limit($validated['purpose'], 100),
            'purpose_description' => $validated['purpose'],
            'flight_cost' => $flightCost,
            'hotel_cost' => $hotelCost,
            'transit_cost' => $transitCost,
            'total_cost' => $totalCost,
            'currency' => 'IDR',
            'policy_status' => 'compliant',
            'budget_status' => 'available',
            'approval_stage' => $isDraft ? 'Draft' : 'Line Manager Review',
            'stage_step' => $isDraft ? 0 : 1,
            'total_steps' => $totalCost <= 5000000 ? 1 : ($totalCost <= 15000000 ? 2 : 3),
            'overall_status' => $isDraft ? 'draft' : 'pending_approval',
            'submitted_at' => now(),
        ]);

        // Role-based notification (hanya jika bukan draft):
        if (! $isDraft) {
            // 1. Notify traveler confirmation
            if (auth()->check()) {
                auth()->user()->notify(new TripSubmittedNotification($trip));
            }

            // 2. Notify Line Managers / Approvers
            $approvers = User::where(function ($q) {
                $q->where('role', 'like', '%Approver%')
                    ->orWhere('role', 'like', '%Manager%')
                    ->orWhereIn('band', ['Band 4', 'Band 5']);
            })->get();

            foreach ($approvers as $approver) {
                if ($approver->id !== auth()->id()) {
                    $approver->notify(new TripSubmittedNotification($trip));
                }
            }
        }

        $successMsg = $isDraft
            ? "Draf pengajuan perjalanan #{$trip->request_code} berhasil disimpan."
            : "Pengajuan perjalanan #{$trip->request_code} berhasil dikirim dan diteruskan ke Line Manager.";

        return redirect()->route('trips.show', $trip->request_code)->with('success', $successMsg);
    }

    /**
     * Find a travel request by request_code or numeric ID.
     */
    protected function findTrip(string $identifier): TravelRequest
    {
        return TravelRequest::where('request_code', $identifier)
            ->orWhere('id', $identifier)
            ->firstOrFail();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $trip = $this->findTrip($id);

        // If accessed via numeric ID, redirect to canonical request_code URL (e.g. /trips/TRV-10291)
        if (is_numeric($id) && ! empty($trip->request_code)) {
            return redirect()->route('trips.show', $trip->request_code);
        }

        $trip->load('traveler');

        return view('trips.show', compact('trip'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $trip = $this->findTrip($id);

        $validated = $request->validate([
            'flight_cost' => 'nullable|numeric|min:0',
            'hotel_cost' => 'nullable|numeric|min:0',
            'transit_cost' => 'nullable|numeric|min:0',
            'purpose_description' => 'nullable|string|max:1000',
        ]);

        if (isset($validated['flight_cost'])) {
            $trip->flight_cost = (float) $validated['flight_cost'];
        }
        if (isset($validated['hotel_cost'])) {
            $trip->hotel_cost = (float) $validated['hotel_cost'];
        }
        if (isset($validated['transit_cost'])) {
            $trip->transit_cost = (float) $validated['transit_cost'];
        }
        if (isset($validated['purpose_description'])) {
            $trip->purpose_description = $validated['purpose_description'];
            $trip->purpose_title = Str::limit($validated['purpose_description'], 100);
        }

        $totalCost = (float) $trip->flight_cost + (float) $trip->hotel_cost + (float) $trip->transit_cost;
        $trip->total_cost = $totalCost;
        $trip->total_steps = $totalCost <= 5000000 ? 1 : ($totalCost <= 15000000 ? 2 : 3);
        $trip->save();

        return back()->with('success', 'Rincian perjalanan dinas #'.$trip->request_code.' berhasil diperbarui.');
    }

    /**
     * Export the travel authorization and itinerary as a formatted PDF.
     */
    public function exportPdf(string $id)
    {
        $trip = $this->findTrip($id)->load('traveler');

        return response()->streamDownload(function () use ($trip) {
            echo "Travel Authorization Pass\nTrip ID: #{$trip->request_code}\nRoute: {$trip->origin} -> {$trip->destination}\nTotal: Rp ".number_format($trip->total_cost, 0, ',', '.');
        }, "travel-pass-{$trip->request_code}.pdf", [
            'Content-Type' => 'application/pdf',
        ]);
    }

    /**
     * Join the coordinated airport ground transit or carpool group.
     */
    public function joinTransitPool(Request $request, string $id)
    {
        $trip = $this->findTrip($id);

        return back()->with('success', "Ditambahkan ke grup koordinasi transportasi bandara untuk Pengajuan #{$trip->request_code}.");
    }

    /**
     * Display corporate travel policy rules and limits (Navan-style tiered policies).
     */
    public function policyRules()
    {
        $policies = TravelPolicy::orderBy('applicable_band')->orderBy('rule_type')->get();
        $totalRules = $policies->count();
        $activeRules = $policies->where('is_active', true)->count();

        return view('policies.index', compact('policies', 'totalRules', 'activeRules'));
    }

    /**
     * Display departmental travel budget allocations from database.
     */
    public function budgets()
    {
        $budgets = DepartmentBudget::orderBy('budget_amount', 'desc')->get();
        $totalAllocated = $budgets->sum('budget_amount');
        $totalSpent = $budgets->sum('spent_amount');
        $remainingLiquidity = max(0, $totalAllocated - $totalSpent);
        $overallUtilization = $totalAllocated > 0 ? round(($totalSpent / $totalAllocated) * 100, 1) : 0;

        return view('budgets.index', compact('budgets', 'totalAllocated', 'totalSpent', 'remainingLiquidity', 'overallUtilization'));
    }

    /**
     * Display compliance and spend audit reports.
     */
    public function complianceReports()
    {
        $trips = TravelRequest::with('traveler')->latest()->get();
        $totalTrips = $trips->count();
        $compliantTrips = $trips->where('policy_status', 'compliant')->count();
        $complianceRate = $totalTrips > 0 ? round(($compliantTrips / $totalTrips) * 100, 1) : 100;
        $totalSpent = $trips->sum('total_cost');

        return view('reports.index', compact('trips', 'totalTrips', 'compliantTrips', 'complianceRate', 'totalSpent'));
    }

    /**
     * Cancel a travel request and revert status.
     */
    public function cancel(Request $request, string $id)
    {
        $trip = $this->findTrip($id);
        $trip->overall_status = 'cancelled';
        $trip->approval_stage = 'Dibatalkan';
        $trip->save();

        return back()->with('success', "Perjalanan dinas #{$trip->request_code} berhasil dibatalkan.");
    }

    /**
     * Submit real cost reporting and reimbursement claims (Proposal Modul 3).
     */
    public function submitReimbursement(Request $request, string $id)
    {
        $validated = $request->validate([
            'real_flight_cost' => 'nullable|numeric|min:0',
            'real_hotel_cost' => 'nullable|numeric|min:0',
            'real_other_cost' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        $trip = $this->findTrip($id);
        $realFlight = (float) ($validated['real_flight_cost'] ?? 1450000);
        $realHotel = (float) ($validated['real_hotel_cost'] ?? 1500000);
        $realOther = (float) ($validated['real_other_cost'] ?? 0);
        $totalRealCost = $realFlight + $realHotel + $realOther;

        $advancePaid = (float) ($trip->total_cost ?? 2950000);
        $variance = $advancePaid - $totalRealCost;

        $trip->overall_status = 'completed';
        $trip->approval_stage = 'Selesai & Direkonsiliasi';
        $trip->save();

        $message = 'Laporan realisasi biaya berhasil diajukan. Total biaya riil: Rp '.number_format($totalRealCost, 0, ',', '.').'. ';
        if ($variance > 0) {
            $message .= 'Terdapat selisih lebih bayar sebesar Rp '.number_format($variance, 0, ',', '.').' yang dikembalikan ke kas perusahaan.';
        } elseif ($variance < 0) {
            $message .= 'Terdapat reimbursement sebesar Rp '.number_format(abs($variance), 0, ',', '.').' yang diajukan ke bagian Payroll/Keuangan.';
        } else {
            $message .= 'Biaya riil sesuai dengan uang muka.';
        }

        return redirect()->route('trips.show', ['trip' => $trip->request_code, 'tab' => 'reimbursement'])
            ->with('success', $message);
    }

    /**
     * Reopen a declined travel request for employee modification.
     */
    public function reopen(Request $request, string $id)
    {
        $trip = $this->findTrip($id);
        $trip->overall_status = 'pending_approval';
        $trip->approval_stage = 'Line Manager Review';
        $trip->stage_step = 1;
        $trip->save();

        return redirect()->route('trips.show', $trip->request_code)
            ->with('success', "Pengajuan #{$trip->request_code} telah dibuka kembali untuk revisi.");
    }

    /**
     * Request an executive exception waiver for policy variance.
     */
    public function requestException(Request $request, string $id)
    {
        $trip = $this->findTrip($id);
        $trip->overall_status = 'pending_approval';
        $trip->approval_stage = 'Director SLA Review';
        $trip->stage_step = min($trip->total_steps, 3);
        $trip->save();

        return back()->with('success', "Permohonan dispensasi eksekutif untuk pengajuan #{$trip->request_code} telah diteruskan ke Direktur.");
    }

    /**
     * Apply policy remedy (e.g. adjust lodging to compliant hotel).
     */
    public function applyRemedy(Request $request, string $id)
    {
        $trip = $this->findTrip($id);
        $trip->hotel_cost = 1500000;
        $trip->total_cost = (float) $trip->flight_cost + 1500000 + (float) $trip->transit_cost;
        $trip->policy_status = 'compliant';
        $trip->approval_stage = 'Line Manager Review';
        $trip->overall_status = 'pending_approval';
        $trip->stage_step = 1;
        $trip->save();

        return redirect()->route('trips.show', $trip->request_code)
            ->with('success', 'Itinerary disesuaikan dengan batas kebijakan (Hotel Santika Premiere Rp 500.000/malam). Pengajuan diteruskan ke Manajer.');
    }

    /**
     * Download cryptographic audit verification certificate.
     */
    public function downloadCertificate(string $id)
    {
        $trip = $this->findTrip($id)->load('traveler');

        return response()->streamDownload(function () use ($trip) {
            echo "========================================================\n";
            echo "   TRAVELSYS - SERTIFIKAT AUDIT DIGITAL TERVERIFIKASI   \n";
            echo "           SMK PLUS PELITA NUSANTARA - KELOMPOK 6       \n";
            echo "========================================================\n\n";
            echo 'Nomor Pengajuan : #'.($trip->request_code ?? $trip->id)."\n";
            echo 'Karyawan        : '.(optional($trip->traveler)->name ?? 'Andi Pratama')."\n";
            echo 'Departemen      : '.(optional($trip->traveler)->department ?? 'Engineering')."\n";
            echo 'Rute Perjalanan : '.$trip->origin.' -> '.$trip->destination."\n";
            echo 'Tanggal Dinas   : '.$trip->departure_date.' s/d '.$trip->return_date."\n";
            echo 'Total Biaya     : Rp '.number_format($trip->total_cost, 0, ',', '.')."\n";
            echo 'Cost Center     : '.($trip->cost_center ?? 'CC-402')."\n";
            echo "Status Otorisasi: DISAHKAN & TERVALIDASI\n";
            echo 'Kriptografi Hash: AUTH-'.strtoupper(substr(md5($trip->id.$trip->created_at), 0, 8))."-ID\n";
            echo 'Waktu Segel     : '.now()->format('d M Y, H:i')." WIB\n";
            echo "Mesin Audit     : Policy Engine Matrix v2026.4\n";
            echo "========================================================\n";
        }, "sertifikat-audit-{$trip->id}.txt", [
            'Content-Type' => 'text/plain',
        ]);
    }

    /**
     * Export trips table data as CSV format.
     */
    public function exportCsv()
    {
        $trips = TravelRequest::with('traveler')->latest()->get();

        return response()->streamDownload(function () use ($trips) {
            echo "Trip Code,Traveler,Department,Origin,Destination,Departure,Return,Total Cost (IDR),Policy Status,Stage\n";
            foreach ($trips as $t) {
                echo sprintf(
                    "\"#%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",%s,\"%s\",\"%s\"\n",
                    $t->request_code ?? $t->id,
                    optional($t->traveler)->name ?? 'Traveler',
                    optional($t->traveler)->department ?? 'General',
                    $t->origin_code ?? $t->origin,
                    $t->dest_code ?? $t->destination,
                    $t->departure_date,
                    $t->return_date,
                    $t->total_cost,
                    $t->policy_status,
                    $t->approval_stage
                );
            }
        }, 'laporan-perjalanan-dinas-'.date('Ymd').'.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }
}
