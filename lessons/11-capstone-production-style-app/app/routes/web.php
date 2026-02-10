<?php

use App\Http\Controllers\CapstoneApplicationController;
use App\Http\Controllers\CapstoneFeatureFlagController;
use App\Http\Controllers\CapstoneJobController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('capstone.dashboard');
});

Route::get('/capstone', [CapstoneFeatureFlagController::class, 'dashboard'])->name('capstone.dashboard');
Route::post('/capstone/flags/{feature}', [CapstoneFeatureFlagController::class, 'updateWeb'])->name('capstone.flags.update');

Route::get('/jobs', [CapstoneJobController::class, 'index'])->name('jobs.index');
Route::get('/jobs/create', [CapstoneJobController::class, 'create'])->name('jobs.create');
Route::post('/jobs', [CapstoneJobController::class, 'store'])->name('jobs.store');
Route::get('/jobs/{job}', [CapstoneJobController::class, 'show'])->name('jobs.show');
Route::post('/jobs/{job}/applications', [CapstoneApplicationController::class, 'store'])->name('jobs.applications.store');
Route::patch('/jobs/{job}/applications/{application}/stage', [CapstoneApplicationController::class, 'updateStage'])->name('jobs.applications.stage');
