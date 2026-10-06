<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerifyOtpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $digits = preg_replace('/\D+/', '', (string) $this->input('mobile')) ?? '';
        if (strlen($digits) === 12 && str_starts_with($digits, '91')) {
            $digits = substr($digits, 2);
        }
        if (strlen($digits) === 11 && str_starts_with($digits, '0')) {
            $digits = substr($digits, 1);
        }

        $this->merge([
            'mobile' => $digits,
            'otp' => preg_replace('/\D+/', '', (string) $this->input('otp')) ?? '',
        ]);
    }

    public function rules(): array
    {
        return [
            'mobile' => ['required', 'digits:10', 'regex:/^[6-9][0-9]{9}$/'],
            'otp' => ['required', 'digits:6'],
        ];
    }
}
