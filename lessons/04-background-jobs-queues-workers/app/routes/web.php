<?php

use App\Http\Controllers\QueueDemoReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/health', fn () => response()->json(['status' => 'ok']));

Route::prefix('queue-demo')->group(function () {
    Route::post('/reports', [QueueDemoReportController::class, 'store']);
    Route::get('/reports/{exportReport}', [QueueDemoReportController::class, 'show']);
    Route::post('/reports/{exportReport}/retry', [QueueDemoReportController::class, 'retry']);
});
