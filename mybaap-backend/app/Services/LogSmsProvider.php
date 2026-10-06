<?php

namespace App\Services;

use App\Contracts\SmsProviderInterface;
use Illuminate\Support\Facades\Log;

class LogSmsProvider implements SmsProviderInterface
{
    public function send(string $mobile, string $code): void
    {
        if (app()->environment('production')) {
            Log::info('OTP dispatched', ['mobile' => $mobile]);

            return;
        }

        Log::info('OTP dispatched', ['mobile' => $mobile, 'code' => $code]);
    }
}
