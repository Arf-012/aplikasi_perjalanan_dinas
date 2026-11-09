<?php

namespace Tests\Feature;

use App\Models\TravelRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class WaterfallApprovalTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test Tier 1 trip (<= 5M) is directly finalized by Line Manager without escalating.
     */
    public function test_tier1_trip_is_finalized_by_line_manager(): void
    {
        $employee = User::where('email', 'andi.pratama@travelsys.internal')->first();
        $manager = User::where('email', 'siti.rahma@travelsys.internal')->first();

        $trip = TravelRequest::create([
            'user_id' => $employee->id,
            'request_code' => 'TRV-TEST-T1',
            'trip_id' => 'TRIP-TEST-T1',
            'cost_center' => 'CC-402',
            'origin' => 'Jakarta (CGK)',
            'origin_code' => 'CGK',
            'destination' => 'Surabaya (SUB)',
            'dest_code' => 'SUB',
            'departure_date' => now()->addDays(5)->toDateString(),
            'return_date' => now()->addDays(7)->toDateString(),
            'purpose_title' => 'Client Visit Tier 1',
            'purpose_description' => 'Tier 1 client visit test',
            'flight_cost' => 1500000.00,
            'hotel_cost' => 1500000.00,
            'total_cost' => 3000000.00,
            'approval_stage' => 'Line Manager Review',
            'overall_status' => 'pending_approval',
        ]);

        $response = $this->actingAs($manager)->post(route('approvals.decision', $trip->id), [
            'action' => 'approve',
        ]);

        $response->assertRedirect();
        $trip->refresh();

        $this->assertEquals('Ticket Issued / Confirmed', $trip->approval_stage);
        $this->assertEquals('booking_ready', $trip->overall_status);
    }

    /**
     * Test Tier 2 trip (5M - 15M) waterfalls from Manager to Finance, then finalizes.
     */
    public function test_tier2_trip_waterfalls_to_finance_and_finalizes_without_director(): void
    {
        $employee = User::where('email', 'andi.pratama@travelsys.internal')->first();
        $manager = User::where('email', 'siti.rahma@travelsys.internal')->first();
        $finance = User::where('email', 'budi.santoso@travelsys.internal')->first();

        $trip = TravelRequest::create([
            'user_id' => $employee->id,
            'request_code' => 'TRV-TEST-T2',
            'trip_id' => 'TRIP-TEST-T2',
            'cost_center' => 'CC-402',
            'origin' => 'Jakarta (CGK)',
            'origin_code' => 'CGK',
            'destination' => 'Surabaya (SUB)',
            'dest_code' => 'SUB',
            'departure_date' => now()->addDays(5)->toDateString(),
            'return_date' => now()->addDays(7)->toDateString(),
            'purpose_title' => 'Client Visit Tier 2',
            'purpose_description' => 'Tier 2 client visit test',
            'flight_cost' => 3500000.00,
            'hotel_cost' => 3550000.00,
            'total_cost' => 7050000.00, // Rp 7.050.000 matches user's screenshot
            'approval_stage' => 'Line Manager Review',
            'overall_status' => 'pending_approval',
        ]);

        // Step 1: Manager approves -> waterfalls to Finance Review
        $res1 = $this->actingAs($manager)->post(route('approvals.decision', $trip->id), [
            'action' => 'approve',
        ]);
        $res1->assertRedirect();
        $trip->refresh();

        $this->assertEquals('Finance Review', $trip->approval_stage);
        $this->assertEquals('pending_approval', $trip->overall_status);

        // Step 2: Finance approves -> finalizes directly to Ticket Issued (bypasses Director)
        $res2 = $this->actingAs($finance)->post(route('approvals.decision', $trip->id), [
            'action' => 'approve',
        ]);
        $res2->assertRedirect();
        $trip->refresh();

        $this->assertEquals('Ticket Issued / Confirmed', $trip->approval_stage);
        $this->assertEquals('booking_ready', $trip->overall_status);
    }

    /**
     * Test regular employee without authority cannot approve requests.
     */
    public function test_unauthorized_user_cannot_approve(): void
    {
        $employee = User::where('email', 'andi.pratama@travelsys.internal')->first();
        $otherEmployee = User::where('email', 'farhan260908@gmail.com')->first();

        $trip = TravelRequest::create([
            'user_id' => $employee->id,
            'request_code' => 'TRV-TEST-UNAUTH',
            'trip_id' => 'TRIP-TEST-UNAUTH',
            'cost_center' => 'CC-402',
            'origin' => 'Jakarta (CGK)',
            'origin_code' => 'CGK',
            'destination' => 'Surabaya (SUB)',
            'dest_code' => 'SUB',
            'departure_date' => now()->addDays(5)->toDateString(),
            'return_date' => now()->addDays(7)->toDateString(),
            'purpose_title' => 'Test Unauth',
            'purpose_description' => 'Test unauth',
            'flight_cost' => 1000000.00,
            'total_cost' => 1000000.00,
            'approval_stage' => 'Line Manager Review',
            'overall_status' => 'pending_approval',
        ]);

        // Attempt by other employee (Band 2)
        $response = $this->actingAs($otherEmployee)->post(route('approvals.decision', $trip->id), [
            'action' => 'approve',
        ]);

        $response->assertSessionHas('error');
        $trip->refresh();
        $this->assertEquals('Line Manager Review', $trip->approval_stage);
    }

    /**
     * Test approver cannot approve their own trip (segregation of duties).
     */
    public function test_approver_cannot_approve_own_trip(): void
    {
        $manager = User::where('email', 'siti.rahma@travelsys.internal')->first();

        $trip = TravelRequest::create([
            'user_id' => $manager->id,
            'request_code' => 'TRV-TEST-SELF',
            'trip_id' => 'TRIP-TEST-SELF',
            'cost_center' => 'CC-201',
            'origin' => 'Jakarta (CGK)',
            'origin_code' => 'CGK',
            'destination' => 'Surabaya (SUB)',
            'dest_code' => 'SUB',
            'departure_date' => now()->addDays(5)->toDateString(),
            'return_date' => now()->addDays(7)->toDateString(),
            'purpose_title' => 'Manager Self Trip',
            'purpose_description' => 'Manager self trip test',
            'flight_cost' => 2000000.00,
            'total_cost' => 2000000.00,
            'approval_stage' => 'Line Manager Review',
            'overall_status' => 'pending_approval',
        ]);

        $response = $this->actingAs($manager)->post(route('approvals.decision', $trip->id), [
            'action' => 'approve',
        ]);

        $response->assertSessionHas('error');
        $trip->refresh();
        $this->assertEquals('Line Manager Review', $trip->approval_stage);
    }
}
