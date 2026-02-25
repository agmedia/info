<?php

use Illuminate\Support\Facades\Route;

Route::get('status', function () {
    return response()->json([
        'ok' => true,
        'service' => 'aginfo',
        'timestamp' => now()->toIso8601String(),
    ]);
});
