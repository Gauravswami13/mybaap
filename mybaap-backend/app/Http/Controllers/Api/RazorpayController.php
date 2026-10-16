<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateRazorpayOrderRequest;
use App\Http\Requests\VerifyRazorpayPaymentRequest;
use App\Http\Resources\UserSubscriptionResource;
use App\Models\SubscriptionPlan;
use App\Services\RazorpayService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class RazorpayController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly RazorpayService $razorpay) {}

    public function order(CreateRazorpayOrderRequest $request): JsonResponse
    {
        $plan = $this->plan($request->integer('plan_id'));

        return $this->ok($this->razorpay->createOrder($request->user(), $plan), 'Razorpay order created.');
    }

    public function verify(VerifyRazorpayPaymentRequest $request): JsonResponse
    {
        $plan = $this->plan($request->integer('plan_id'));
        $subscription = $this->razorpay->verify(
            $request->user(),
            $plan,
            $request->string('razorpay_order_id')->toString(),
            $request->string('razorpay_payment_id')->toString(),
            $request->string('razorpay_signature')->toString(),
        );

        return $this->ok(new UserSubscriptionResource($subscription), 'Subscription activated.');
    }

    private function plan(int $id): SubscriptionPlan
    {
        $plan = SubscriptionPlan::query()->where('is_active', true)->find($id);
        if ($plan === null) {
            throw new ApiException('Plan is not available.', 422);
        }

        return $plan;
    }
}
