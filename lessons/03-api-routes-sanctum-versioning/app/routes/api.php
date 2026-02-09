<?php

use App\Http\Controllers\Api\V1\AuthTokenController;
use App\Http\Controllers\Api\V1\ProjectController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('tokens', [AuthTokenController::class, 'store']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::delete('tokens/current', [AuthTokenController::class, 'destroy']);
        Route::apiResource('projects', ProjectController::class);
    });
});
