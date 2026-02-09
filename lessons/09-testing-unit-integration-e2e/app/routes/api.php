<?php

use App\Http\Controllers\Api\IssueApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::post('/issues', [IssueApiController::class, 'store']);
    Route::get('/issues/{issue}', [IssueApiController::class, 'show']);
    Route::patch('/issues/{issue}/resolve', [IssueApiController::class, 'resolve']);
});
