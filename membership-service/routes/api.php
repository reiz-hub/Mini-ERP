<?php

use App\Http\Controllers\Api\V1\MembershipController;
use App\Http\Controllers\Api\V1\PlanController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware('auth.jwt')->group(function () {
    // Plans
    Route::get('/plans', [PlanController::class, 'index']);
    Route::post('/plans', [PlanController::class, 'store']);
    Route::get('/plans/{id}', [PlanController::class, 'show']);
    Route::put('/plans/{id}', [PlanController::class, 'update']);
    Route::delete('/plans/{id}', [PlanController::class, 'destroy']);

    // Memberships
    Route::get('/memberships', [MembershipController::class, 'index']);
    Route::post('/memberships/enroll', [MembershipController::class, 'enroll']);
    Route::put('/memberships/{id}/renew', [MembershipController::class, 'renew']);
    Route::get('/memberships/expiring', [MembershipController::class, 'expiring']);
});
