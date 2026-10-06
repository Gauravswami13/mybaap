<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendOtpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'mobile' => $this->normalizeMobile($this->input('mobile')),
        ]);
    }

    public function rules(): array
    {
        return [
            'mobile' => ['required', 'digits:10', 'regex:/^[6-9][0-9]{9}$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'mobile.digits' => 'Enter a 10 digit mobile number.',
            'mobile.regex' => 'Enter a valid mobile number.',
        ];
    }

    private function normalizeMobile(mixed $value): string
    {
        $digits = preg_replace('/\D+/', '', (string) $value) ?? '';
        if (strlen($digits) === 12 && str_starts_with($digits, '91')) {
            return substr($digits, 2);
        }
        if (strlen($digits) === 11 && str_starts_with($digits, '0')) {
            return substr($digits, 1);
        }

        return $digits;
    }
}
