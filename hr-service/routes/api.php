<?php

use App\Http\Controllers\Api\V1\EmployeeController;
use App\Http\Controllers\Api\V1\TrainerAssignmentController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware('auth.jwt')->group(function () {
    // Employees
    Route::get('/employees', [EmployeeController::class, 'index']);
    Route::post('/employees', [EmployeeController::class, 'store']);
    Route::get('/employees/{id}', [EmployeeController::class, 'show']);
    Route::put('/employees/{id}', [EmployeeController::class, 'update']);
    Route::delete('/employees/{id}', [EmployeeController::class, 'destroy']);

    // Trainer Assignments
    Route::get('/assignments', [TrainerAssignmentController::class, 'index']);
    Route::post('/assignments', [TrainerAssignmentController::class, 'store']);
    Route::get('/assignments/{id}', [TrainerAssignmentController::class, 'show']);
    Route::put('/assignments/{id}', [TrainerAssignmentController::class, 'update']);
    Route::delete('/assignments/{id}', [TrainerAssignmentController::class, 'destroy']);
});

// Health check (public, no auth required)
Route::get('/health', function () {
    return response()->json(['status' => 'ok', 'service' => 'hr-service']);
});
