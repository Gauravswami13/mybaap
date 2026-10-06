<?php

namespace App\Services;

use App\Contracts\SmsProviderInterface;
use App\Exceptions\ApiException;
use App\Models\Otp;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class OtpService
{
    public function __construct(private readonly SmsProviderInterface $sms) {}

    public function send(string $mobile): void
    {
        $code = $this->code();

        Otp::query()->create([
            'mobile' => $mobile,
            'code_hash' => Hash::make($code),
            'expires_at' => now()->addMinutes((int) config('otp.expiry_minutes', 5)),
            'attempts' => 0,
        ]);

        $this->sms->send($mobile, $code);
    }

    public function verify(string $mobile, string $code): User
    {
        $otp = Otp::query()
            ->where('mobile', $mobile)
            ->whereNull('verified_at')
            ->latest('id')
            ->first();

        if ($otp === null || $otp->expires_at->isPast()) {
            throw new ApiException('Invalid or expired OTP.', 401);
        }

        $maxAttempts = (int) config('otp.max_attempts', 5);
        if ($otp->attempts >= $maxAttempts) {
            throw new ApiException('Too many attempts. Request a new OTP.', 429);
        }

        if (! Hash::check($code, $otp->code_hash)) {
            $otp->increment('attempts');

            throw new ApiException('Invalid or expired OTP.', 401);
        }

        $otp->forceFill(['verified_at' => now()])->save();

        $user = User::query()->firstOrCreate(
            ['mobile' => $mobile],
            ['name' => null, 'status' => User::STATUS_ACTIVE],
        );

        if ($user->isBlocked()) {
            throw new ApiException('Your account is blocked.', 403);
        }

        return $user;
    }

    private function code(): string
    {
        if (! app()->environment('production') && (app()->environment('local', 'testing') || config('app.debug'))) {
            $fixed = (string) config('otp.fixed_code');
            if (preg_match('/^\d{6}$/', $fixed) === 1) {
                return $fixed;
            }
        }

        return str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }
}
