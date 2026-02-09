<?php

use App\Http\Controllers\SchedulerStatusController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/health', fn () => response()->json(['status' => 'ok']));
Route::get('/scheduler/status', SchedulerStatusController::class);
