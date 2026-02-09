<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'message' => 'Lesson 10 deployment app',
        'docs' => '/health',
    ]);
});

Route::get('/health', function () {
    try {
        DB::connection()->getPdo();
        $database = 'ok';
    } catch (Throwable $exception) {
        $database = 'error';
    }

    return response()->json([
        'status' => $database === 'ok' ? 'ok' : 'degraded',
        'app_env' => app()->environment(),
        'database' => $database,
        'timestamp' => now()->toIso8601String(),
    ], $database === 'ok' ? 200 : 503);
});
