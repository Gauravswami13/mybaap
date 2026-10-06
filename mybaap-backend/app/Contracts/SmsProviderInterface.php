<?php

namespace App\Contracts;

interface SmsProviderInterface
{
    public function send(string $mobile, string $code): void;
}
