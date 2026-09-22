<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TravelRequest;

class TravelRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = TravelRequest::with('traveler');

        if ($request->filled('q')) {
            $query->where('destination', 'like', '%' . $request->q . '%')
                  ->orWhere('id', 'like', '%' . $request->q . '%');
        }

        $trips = $query->latest()->paginate(10);

        return view('trips.index', compact('trips'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('trips.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cost_center_id' => 'required|string',
            'origin_code'    => 'required|string|size:3',
            'dest_code'      => 'required|string|size:3',
            'departure_date' => 'required|date',
            'return_date'    => 'required|date|after_or_equal:departure_date',
            'purpose'        => 'required|string|max:1000',
            'flight_cost'    => 'nullable|numeric',
            'hotel_cost'     => 'nullable|numeric',
            'per_diem_cost'  => 'nullable|numeric',
        ]);

        $trip = TravelRequest::create([
            'user_id'        => auth()->id() ?? 1,
            'cost_center_id' => $validated['cost_center_id'],
            'origin_code'    => $validated['origin_code'],
            'dest_code'      => $validated['dest_code'],
            'origin'         => $validated['origin_code'] === 'CGK' ? 'Jakarta' : 'Surabaya',
            'destination'    => $validated['dest_code'] === 'SUB' ? 'Surabaya' : 'Singapore',
            'departure_date' => $validated['departure_date'],
            'return_date'    => $validated['return_date'],
            'purpose'        => $validated['purpose'],
            'estimated_cost' => ($validated['flight_cost'] ?? 0) + ($validated['hotel_cost'] ?? 0) + ($validated['per_diem_cost'] ?? 0),
            'approval_status' => 'Pending Line Manager',
            'policy_status'  => 'compliant',
        ]);

        return redirect()->route('trips.show', $trip->id)
                         ->with('success', "Travel Request #{$trip->id} submitted successfully and routed to Line Manager.");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $trip = TravelRequest::with('traveler')->findOrFail($id);

        return view('trips.show', compact('trip'));
    }
}
