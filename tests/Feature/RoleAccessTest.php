<?php

namespace Tests\Feature;

use App\Models\TravelRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test unauthenticated guest cannot access approvals and is redirected to login.
     */
    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('approvals.index'));
        $response->assertRedirect(route('login'));
    }

    /**
     * Test regular employee is blocked from accessing approvals queue and redirected to dashboard.
     */
    public function test_employee_cannot_access_approvals_queue(): void
    {
        $employee = User::where('email', 'andi.pratama@travelsys.internal')->first();

        $response = $this->actingAs($employee)->get(route('approvals.index'));

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('error');
    }

    /**
     * Test regular employee cannot access trip approval detail.
     */
    public function test_employee_cannot_access_trip_approval_detail(): void
    {
        $employee = User::where('email', 'andi.pratama@travelsys.internal')->first();
        $trip = TravelRequest::first();

        $response = $this->actingAs($employee)->get(route('approvals.show', $trip->id));

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('error');
    }

    /**
     * Test employee cannot submit approval decision.
     */
    public function test_employee_cannot_submit_approval_decision(): void
    {
        $employee = User::where('email', 'andi.pratama@travelsys.internal')->first();
        $trip = TravelRequest::where('approval_stage', 'Line Manager Review')->first() ?? TravelRequest::first();

        $originalStage = $trip->approval_stage;

        $response = $this->actingAs($employee)->post(route('approvals.decision', $trip->id), [
            'action' => 'approve',
        ]);

        $response->assertSessionHas('error');
        $trip->refresh();
        $this->assertEquals($originalStage, $trip->approval_stage);
    }

    /**
     * Test Manager role CAN access approvals queue.
     */
    public function test_manager_can_access_approvals(): void
    {
        $manager = User::where('email', 'siti.rahma@travelsys.internal')->first();

        $response = $this->actingAs($manager)->get(route('approvals.index'));
        $response->assertOk();
    }

    /**
     * Test Finance role CAN access approvals queue.
     */
    public function test_finance_can_access_approvals(): void
    {
        $finance = User::where('email', 'budi.santoso@travelsys.internal')->first();

        $response = $this->actingAs($finance)->get(route('approvals.index'));
        $response->assertOk();
    }

    /**
     * Test Travel Admin role CAN access approvals queue.
     */
    public function test_travel_admin_can_access_approvals(): void
    {
        $admin = User::where('email', 'admin@travelsys.internal')->first();

        $response = $this->actingAs($admin)->get(route('approvals.index'));
        $response->assertOk();
    }

    /**
     * Test Direktur role CAN access approvals queue.
     */
    public function test_direktur_can_access_approvals(): void
    {
        $direktur = User::where('email', 'hendra.gunawan@travelsys.internal')->first();

        $response = $this->actingAs($direktur)->get(route('approvals.index'));
        $response->assertOk();
    }

    /**
     * Test navigation view renders Persetujuan link only for approvers and hides it for employees.
     */
    public function test_navigation_renders_persetujuan_only_for_approvers(): void
    {
        $employee = User::where('email', 'andi.pratama@travelsys.internal')->first();
        $manager = User::where('email', 'siti.rahma@travelsys.internal')->first();

        // 1. Employee view should not contain link to approvals.index
        $viewAsEmployee = $this->actingAs($employee)->view('layouts.navigation');
        $viewAsEmployee->assertDontSee(route('approvals.index'));

        // 2. Manager view should contain link to approvals.index
        $viewAsManager = $this->actingAs($manager)->view('layouts.navigation');
        $viewAsManager->assertSee(route('approvals.index'));
    }

    /**
     * Test login page renders all 5 demo accounts for quick pick.
     */
    public function test_login_page_renders_all_5_demo_accounts(): void
    {
        $response = $this->get(route('login'));
        $response->assertOk();

        // Check for all 5 demo emails
        $response->assertSee('andi.pratama@travelsys.internal');
        $response->assertSee('siti.rahma@travelsys.internal');
        $response->assertSee('budi.santoso@travelsys.internal');
        $response->assertSee('admin@travelsys.internal');
        $response->assertSee('hendra.gunawan@travelsys.internal');

        // Check for all 5 role titles
        $response->assertSee('Employee');
        $response->assertSee('Manager');
        $response->assertSee('Finance');
        $response->assertSee('Travel Admin');
        $response->assertSee('Direktur');
    }
}
