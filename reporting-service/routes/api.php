<?php

use App\Http\Controllers\Api\V1\ReportController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware('auth.jwt')->group(function () {
    Route::get('/reports/summary', [ReportController::class, 'summary']);
});

// Health check (public, no auth required)
Route::get('/health', function () {
    return response()->json(['status' => 'ok', 'service' => 'reporting-service']);
});
