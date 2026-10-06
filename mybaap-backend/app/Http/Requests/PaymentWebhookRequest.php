<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentWebhookRequest extends FormRequest
{
    public function authorize(): bool
    {
        $secret = (string) config('services.payment.webhook_secret');
        $signature = (string) $this->header('X-Webhook-Signature', '');
        if ($secret === '' || $signature === '') {
            return false;
        }

        $expected = hash_hmac('sha256', $this->getContent(), $secret);

        return hash_equals($expected, $signature);
    }

    public function rules(): array
    {
        return [
            'mobile' => ['required', 'digits:10'],
            'plan_id' => ['required', 'integer', 'exists:subscription_plans,id'],
            'payment_ref' => ['required', 'string', 'max:120'],
        ];
    }

    protected function failedAuthorization(): void
    {
        abort(401, 'Invalid webhook signature.');
    }
}
