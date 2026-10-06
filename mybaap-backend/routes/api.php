<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProfileController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/send-otp', [AuthController::class, 'sendOtp'])->middleware('throttle:send-otp');
Route::post('/auth/verify-otp', [AuthController::class, 'verifyOtp'])->middleware('throttle:verify-otp');

Route::middleware(['auth:sanctum', 'active'])->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);
});
