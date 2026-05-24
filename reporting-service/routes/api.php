<?php

use App\Http\Controllers\Api\V1\ReportController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware('auth.jwt')->group(function () {
    Route::get('/reports/summary', [ReportController::class, 'summary']);
});
