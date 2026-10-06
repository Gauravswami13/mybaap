<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CatalogController;
use App\Http\Controllers\Api\ContentController;
use App\Http\Controllers\Api\PaymentWebhookController;
use App\Http\Controllers\Api\PlaybackController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\SubscriptionController;
use Illuminate\Support\Facades\Route;

Route::get('/pages/{slug}', [ContentController::class, 'page'])->where('slug', '[a-z0-9\-]+');
Route::get('/faqs', [ContentController::class, 'faqs']);

Route::post('/auth/send-otp', [AuthController::class, 'sendOtp'])->middleware('throttle:send-otp');
Route::post('/auth/verify-otp', [AuthController::class, 'verifyOtp'])->middleware('throttle:verify-otp');
Route::post('/payments/webhook', [PaymentWebhookController::class, 'handle'])->middleware('throttle:30,1');
Route::get('/media/videos/{id}', [PlaybackController::class, 'stream'])
    ->middleware('signed')
    ->whereNumber('id')
    ->name('videos.stream');

Route::middleware(['auth:sanctum', 'active'])->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);

    Route::get('/home', [CatalogController::class, 'home']);
    Route::get('/categories', [CatalogController::class, 'categories']);
    Route::get('/videos', [CatalogController::class, 'videos']);
    Route::get('/videos/{id}/playback', [PlaybackController::class, 'show'])->whereNumber('id');
    Route::get('/videos/{id}', [CatalogController::class, 'show'])->whereNumber('id');

    Route::get('/plans', [SubscriptionController::class, 'plans']);
    Route::get('/subscription', [SubscriptionController::class, 'current']);
});
