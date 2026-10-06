<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SendOtpRequest;
use App\Http\Requests\VerifyOtpRequest;
use App\Http\Resources\UserResource;
use App\Services\OtpService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly OtpService $otp) {}

    public function sendOtp(SendOtpRequest $request): JsonResponse
    {
        $this->otp->send($request->string('mobile')->toString());

        return $this->ok([
            'mobile' => $request->string('mobile')->toString(),
            'resend_in' => (int) config('otp.resend_seconds', 30),
        ], 'OTP sent.');
    }

    public function verifyOtp(VerifyOtpRequest $request): JsonResponse
    {
        $user = $this->otp->verify(
            $request->string('mobile')->toString(),
            $request->string('otp')->toString(),
        );

        $token = $user->createToken('android')->plainTextToken;

        return $this->ok([
            'token' => $token,
            'user' => new UserResource($user),
        ], 'Verified successfully.');
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()?->currentAccessToken()?->delete();

        return $this->ok((object) [], 'Logged out.');
    }
}
