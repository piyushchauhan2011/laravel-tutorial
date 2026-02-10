<?php

use App\Http\Controllers\Api\CapstoneAnalyticsApiController;
use App\Http\Controllers\Api\CapstoneJobApiController;
use App\Http\Controllers\CapstoneFeatureFlagController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::get('/feature-flags', [CapstoneFeatureFlagController::class, 'indexApi']);
    Route::patch('/feature-flags/{feature}', [CapstoneFeatureFlagController::class, 'updateApi']);

    Route::get('/jobs', [CapstoneJobApiController::class, 'indexJobs']);
    Route::post('/jobs', [CapstoneJobApiController::class, 'storeJob']);
    Route::get('/jobs/{job}', [CapstoneJobApiController::class, 'showJob']);
    Route::get('/jobs/{job}/applications', [CapstoneJobApiController::class, 'indexApplications']);
    Route::post('/jobs/{job}/applications', [CapstoneJobApiController::class, 'storeApplication']);
    Route::patch('/applications/{application}/stage', [CapstoneJobApiController::class, 'updateApplicationStage']);

    Route::get('/analytics/pipeline', [CapstoneAnalyticsApiController::class, 'pipeline']);
});
