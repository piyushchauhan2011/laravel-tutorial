<?php

use App\Http\Controllers\IssueController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('issues.index');
});

Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'lesson' => '09-testing-unit-integration-e2e',
    ]);
});

Route::get('/issues', [IssueController::class, 'index'])->name('issues.index');
Route::get('/issues/create', [IssueController::class, 'create'])->name('issues.create');
Route::post('/issues', [IssueController::class, 'store'])->name('issues.store');
Route::get('/issues/{issue}', [IssueController::class, 'show'])->name('issues.show');
Route::patch('/issues/{issue}/resolve', [IssueController::class, 'resolve'])->name('issues.resolve');
