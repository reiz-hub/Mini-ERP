<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GatewayController;
use Illuminate\Support\Facades\Route;

// Guest Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// Protected Web Routes
Route::middleware('auth.jwt.session')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Dashboard
    Route::get('/', [GatewayController::class, 'dashboard'])->name('dashboard');

    // Members (CRM)
    Route::get('/members', [GatewayController::class, 'members'])->name('members');
    Route::post('/members', [GatewayController::class, 'createMember'])->name('members.create');
    Route::post('/members/{id}/update', [GatewayController::class, 'updateMember'])->name('members.update');
    Route::post('/members/{id}/delete', [GatewayController::class, 'deleteMember'])->name('members.delete');

    // Memberships & Plans
    Route::get('/memberships', [GatewayController::class, 'memberships'])->name('memberships');
    Route::post('/plans', [GatewayController::class, 'createPlan'])->name('plans.create');
    Route::post('/memberships/enroll', [GatewayController::class, 'enroll'])->name('memberships.enroll');
    Route::post('/memberships/{id}/renew', [GatewayController::class, 'renew'])->name('memberships.renew');

    // Employees & Trainer Assignments (HR)
    Route::get('/employees', [GatewayController::class, 'employees'])->name('employees');
    Route::post('/employees', [GatewayController::class, 'createEmployee'])->name('employees.create');
    Route::post('/assignments', [GatewayController::class, 'assignTrainer'])->name('assignments.create');

    // Reports Summary
    Route::get('/reports', [GatewayController::class, 'reports'])->name('reports');
});

// Health check (public, no auth required)
Route::get('/health', function () {
    return response()->json(['status' => 'ok', 'service' => 'api-gateway']);
});
