<?php

return [
    'fixed_code' => env('OTP_FIXED_CODE', '123456'),
    'expiry_minutes' => 5,
    'max_attempts' => 5,
    'resend_seconds' => 30,
];
