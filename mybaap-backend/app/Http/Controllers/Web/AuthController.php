<?php

namespace App\Http\Controllers\Web;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\SendOtpRequest;
use App\Http\Requests\VerifyOtpRequest;
use App\Services\OtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function __construct(private readonly OtpService $otp) {}

    public function show(): View
    {
        return view('site.login');
    }

    public function send(SendOtpRequest $request): RedirectResponse
    {
        try {
            $this->otp->send($request->string('mobile')->toString());
        } catch (ApiException $exception) {
            return back()->withInput()->with('error', $exception->getMessage());
        }

        $request->session()->put('otp_mobile', $request->string('mobile')->toString());

        $message = app()->environment('production')
            ? 'OTP sent.'
            : 'OTP sent. Testing code is 123456.';

        return redirect()->route('login.otp')->with('status', $message);
    }

    public function otp(Request $request): View|RedirectResponse
    {
        if (! is_string($request->session()->get('otp_mobile'))) {
            return redirect()->route('login');
        }

        return view('site.otp', [
            'mobile' => $request->session()->get('otp_mobile'),
        ]);
    }

    public function verify(VerifyOtpRequest $request): RedirectResponse
    {
        try {
            $user = $this->otp->verify(
                $request->string('mobile')->toString(),
                $request->string('otp')->toString(),
            );
        } catch (ApiException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        Auth::login($user);
        $request->session()->forget('otp_mobile');
        $request->session()->regenerate();

        return redirect()->intended(route('account'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
