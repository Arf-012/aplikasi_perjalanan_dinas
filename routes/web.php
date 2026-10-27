<?php

use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\TravelRequestController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Corporate Travel Management System (TravelSys)
|--------------------------------------------------------------------------
| Tech stack: Laravel 11/12 + Blade Templates + Tailwind CSS
*/

Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Guest Authentication Routes
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Authenticated Routes
Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Executive Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Travel Requests (Trips) Resource & Endpoints
    Route::resource('trips', TravelRequestController::class);
    Route::get('/trips/{trip}/export', [TravelRequestController::class, 'exportPdf'])->name('trips.export');
    Route::get('/trips/{trip}/certificate', [TravelRequestController::class, 'downloadCertificate'])->name('trips.certificate');
    Route::post('/trips/{trip}/pool-join', [TravelRequestController::class, 'joinTransitPool'])->name('trips.pool.join');
    Route::post('/trips/{trip}/cancel', [TravelRequestController::class, 'cancel'])->name('trips.cancel');
    Route::post('/trips/{trip}/reimbursement', [TravelRequestController::class, 'submitReimbursement'])->name('trips.reimbursement');
    Route::post('/trips/{trip}/reopen', [TravelRequestController::class, 'reopen'])->name('trips.reopen');
    Route::post('/trips/{trip}/exception', [TravelRequestController::class, 'requestException'])->name('trips.exception');
    Route::post('/trips/{trip}/remedy', [TravelRequestController::class, 'applyRemedy'])->name('trips.remedy');
    Route::get('/trips-export-csv', [TravelRequestController::class, 'exportCsv'])->name('trips.export.csv');

    // Approvals & Decision Center
    Route::middleware(['role:Manager,Finance,Director,Admin'])
        ->group(function () {
            Route::get('/approvals', [ApprovalController::class, 'index'])
                ->name('approvals.index');

            Route::get('/approvals/{trip}', [ApprovalController::class, 'show'])
                ->name('approvals.show');

            Route::post('/approvals/{trip}/decision', [ApprovalController::class, 'recordDecision'])
                ->name('approvals.decision');
        });

    // Policies & Budgets
    Route::get('/policies', [TravelRequestController::class, 'policyRules'])->name('policies.index');
    Route::get('/budgets', [TravelRequestController::class, 'budgets'])->name('budgets.index');
    Route::get('/reports', [TravelRequestController::class, 'complianceReports'])->name('reports.index');

    // Notifications Center
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
});
