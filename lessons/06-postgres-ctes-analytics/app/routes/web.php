<?php

use App\Http\Controllers\AnalyticsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/health', fn () => response()->json(['status' => 'ok']));
Route::get('/analytics/revenue', [AnalyticsController::class, 'revenue']);
Route::get('/analytics/category-rollup', [AnalyticsController::class, 'categoryRollup']);
