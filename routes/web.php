<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TravelRequestController;
use App\Http\Controllers\ApprovalController;

/*
|--------------------------------------------------------------------------
| Web Routes - Corporate Travel Management System (TravelSys)
|--------------------------------------------------------------------------
| Tech stack: Laravel 11/12 + Blade Templates + Tailwind CSS
*/

Route::get('/', function () {
    return redirect()->route('dashboard');
});

    // Executive Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Travel Requests (Trips) Resource
    Route::resource('trips', TravelRequestController::class);
    Route::get('/trips/{trip}/export', [TravelRequestController::class, 'exportPdf'])->name('trips.export');
    Route::post('/trips/{trip}/pool-join', [TravelRequestController::class, 'joinTransitPool'])->name('trips.pool.join');

    // Approvals & Decision Center
    Route::get('/approvals', [ApprovalController::class, 'index'])->name('approvals.index');
    Route::get('/approvals/{trip}', [ApprovalController::class, 'show'])->name('approvals.show');
    Route::post('/approvals/{trip}/decision', [ApprovalController::class, 'recordDecision'])->name('approvals.decision');

    // Policies & Budgets
    Route::get('/policies', [TravelRequestController::class, 'policyRules'])->name('policies.index');
    Route::get('/budgets', [TravelRequestController::class, 'budgets'])->name('budgets.index');
    Route::get('/reports', [TravelRequestController::class, 'complianceReports'])->name('reports.index');
