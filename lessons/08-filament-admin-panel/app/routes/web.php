<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin');
});

Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'lesson' => '08-filament-admin-panel',
    ]);
});
