<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SettingsService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class AppController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly SettingsService $settings) {}

    public function bootstrap(): JsonResponse
    {
        return $this->ok($this->settings->bootstrap());
    }
}
