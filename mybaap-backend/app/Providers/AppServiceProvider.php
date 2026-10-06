<?php

namespace App\Providers;

use App\Contracts\SmsProviderInterface;
use App\Services\LogSmsProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(SmsProviderInterface::class, LogSmsProvider::class);
    }

    public function boot(): void
    {
        RateLimiter::for('send-otp', function (Request $request) {
            $mobile = preg_replace('/\D+/', '', (string) $request->input('mobile'));

            return Limit::perMinutes(10, 5)->by($request->ip().'|'.$mobile);
        });

        RateLimiter::for('verify-otp', function (Request $request) {
            $mobile = preg_replace('/\D+/', '', (string) $request->input('mobile'));

            return Limit::perMinutes(10, 10)->by($request->ip().'|'.$mobile);
        });
    }
}
