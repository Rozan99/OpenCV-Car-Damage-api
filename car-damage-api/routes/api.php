<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DamageController;

Route::post('/upload', [DamageController::class, 'analyze']);
Route::get('/logs', [DamageController::class, 'logs']);

// For testing
Route::get('/test', function () {
    return response()->json(['message' => 'API is working']);
});
