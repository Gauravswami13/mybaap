<?php

use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/privacy-policy', [PageController::class, 'show'])->defaults('slug', 'privacy-policy');
Route::get('/terms', [PageController::class, 'show'])->defaults('slug', 'terms');
Route::view('/delete-account', 'pages.delete-account');
