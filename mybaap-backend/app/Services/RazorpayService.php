<?php

namespace App\Services;

use App\Exceptions\ApiException;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class RazorpayService
{
    public function __construct(private readonly SubscriptionService $subscriptions) {}

    public function createOrder(User $user, SubscriptionPlan $plan): array
    {
        [$key, $secret] = $this->keys();
        $amount = $this->amountPaise($plan);
        $receipt = substr('mb'.$user->id.'p'.$plan->id.'t'.now()->timestamp, 0, 40);

        try {
            $response = Http::withBasicAuth($key, $secret)
                ->acceptJson()
                ->timeout(15)
                ->post('https://api.razorpay.com/v1/orders', [
                    'amount' => $amount,
                    'currency' => 'INR',
                    'receipt' => $receipt,
                    'notes' => [
                        'plan_id' => (string) $plan->id,
                        'user_id' => (string) $user->id,
                    ],
                ]);
        } catch (ConnectionException) {
            throw new ApiException('Could not reach Razorpay. Try again.', 502);
        }

        if (! $response->successful() || ! is_string($response->json('id'))) {
            $description = $response->json('error.description');
            $detail = is_string($description) && $description !== '' ? $description : 'Could not start Razorpay checkout.';

            throw new ApiException($detail, 502);
        }

        return [
            'key_id' => $key,
            'order_id' => $response->json('id'),
            'amount' => $amount,
            'currency' => 'INR',
            'plan_name' => $plan->name,
        ];
    }

    public function verify(User $user, SubscriptionPlan $plan, string $orderId, string $paymentId, string $signature): UserSubscription
    {
        [$key, $secret] = $this->keys();
        $expected = hash_hmac('sha256', $orderId.'|'.$paymentId, $secret);
        if (! hash_equals($expected, $signature)) {
            throw new ApiException('Payment signature is invalid.', 401);
        }

        $payment = Http::withBasicAuth($key, $secret)
            ->acceptJson()
            ->get('https://api.razorpay.com/v1/payments/'.$paymentId);
        if (! $payment->successful()) {
            throw new ApiException('Payment could not be verified.', 502);
        }
        if ($payment->json('order_id') !== $orderId) {
            throw new ApiException('Payment does not match this order.', 422);
        }
        if (! in_array($payment->json('status'), ['captured', 'authorized'], true)) {
            throw new ApiException('Payment is not complete.', 422);
        }
        if ((int) $payment->json('amount') !== $this->amountPaise($plan)) {
            throw new ApiException('Paid amount does not match the plan.', 422);
        }

        $order = Http::withBasicAuth($key, $secret)
            ->acceptJson()
            ->get('https://api.razorpay.com/v1/orders/'.$orderId);
        if (! $order->successful()) {
            throw new ApiException('Order could not be verified.', 502);
        }
        $notes = $order->json('notes');
        if (! is_array($notes) || (string) ($notes['plan_id'] ?? '') !== (string) $plan->id || (string) ($notes['user_id'] ?? '') !== (string) $user->id) {
            throw new ApiException('This payment is not for your selected plan.', 422);
        }

        return $this->subscriptions->activate($user, $plan, $paymentId);
    }

    private function amountPaise(SubscriptionPlan $plan): int
    {
        $amount = (int) round(((float) $plan->price) * 100);
        if ($amount < 100) {
            throw new ApiException('Plan amount is too small for Razorpay.', 422);
        }

        return $amount;
    }

    private function keys(): array
    {
        $key = (string) config('services.razorpay.key_id');
        $secret = (string) config('services.razorpay.key_secret');
        if ($key === '' || $secret === '') {
            throw new ApiException('Razorpay test keys are not configured.', 503);
        }

        return [$key, $secret];
    }
}
