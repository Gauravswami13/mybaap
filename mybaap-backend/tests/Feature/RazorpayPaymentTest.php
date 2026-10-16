<?php

namespace Tests\Feature;

use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class RazorpayPaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_verified_razorpay_payment_activates_the_plan(): void
    {
        config([
            'services.razorpay.key_id' => 'rzp_test_key',
            'services.razorpay.key_secret' => 'test_secret',
        ]);

        $plan = SubscriptionPlan::query()->create([
            'name' => 'Monthly',
            'price' => 99,
            'duration_days' => 30,
            'is_active' => true,
            'sort_order' => 1,
        ]);
        $user = User::factory()->create();
        $signature = hash_hmac('sha256', 'order_test|pay_test', 'test_secret');

        Http::fake(function ($request) use ($plan, $user) {
            $url = $request->url();
            if ($request->method() === 'POST' && str_ends_with($url, '/orders')) {
                return Http::response(['id' => 'order_test', 'amount' => 9900], 200);
            }
            if (str_contains($url, '/payments/')) {
                return Http::response([
                    'id' => 'pay_test',
                    'order_id' => 'order_test',
                    'status' => 'captured',
                    'amount' => 9900,
                ], 200);
            }
            if (str_contains($url, '/orders/')) {
                return Http::response([
                    'id' => 'order_test',
                    'amount' => 9900,
                    'notes' => [
                        'plan_id' => (string) $plan->id,
                        'user_id' => (string) $user->id,
                    ],
                ], 200);
            }

            return Http::response([], 404);
        });

        $this->actingAs($user)
            ->postJson('/api/v1/payments/razorpay/order', ['plan_id' => $plan->id])
            ->assertOk()
            ->assertJsonPath('data.order_id', 'order_test')
            ->assertJsonPath('data.amount', 9900);

        $this->actingAs($user)
            ->postJson('/api/v1/payments/razorpay/verify', [
                'plan_id' => $plan->id,
                'razorpay_order_id' => 'order_test',
                'razorpay_payment_id' => 'pay_test',
                'razorpay_signature' => $signature,
            ])
            ->assertOk()
            ->assertJsonPath('data.payment_ref', 'pay_test')
            ->assertJsonPath('data.is_active', true);

        $this->actingAs($user)
            ->postJson('/api/v1/payments/razorpay/verify', [
                'plan_id' => $plan->id,
                'razorpay_order_id' => 'order_test',
                'razorpay_payment_id' => 'pay_test',
                'razorpay_signature' => 'bad',
            ])
            ->assertUnauthorized();
    }
}
