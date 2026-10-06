<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentWebhookRequest;
use App\Http\Resources\UserSubscriptionResource;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Services\SubscriptionService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class PaymentWebhookController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly SubscriptionService $subscriptions) {}

    public function handle(PaymentWebhookRequest $request): JsonResponse
    {
        $user = User::query()->where('mobile', $request->string('mobile')->toString())->first();
        if ($user === null) {
            throw new ApiException('User not found.', 404);
        }

        $plan = SubscriptionPlan::query()
            ->where('is_active', true)
            ->find($request->integer('plan_id'));
        if ($plan === null) {
            throw new ApiException('Plan is not available.', 422);
        }

        $subscription = $this->subscriptions->activate(
            $user,
            $plan,
            $request->string('payment_ref')->toString(),
        );

        return $this->ok(new UserSubscriptionResource($subscription), 'Subscription activated.');
    }
}
