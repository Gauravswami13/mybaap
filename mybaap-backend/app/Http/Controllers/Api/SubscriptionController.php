<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SubscriptionPlanResource;
use App\Http\Resources\UserSubscriptionResource;
use App\Services\SubscriptionService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly SubscriptionService $subscriptions) {}

    public function plans(): JsonResponse
    {
        return $this->ok(SubscriptionPlanResource::collection($this->subscriptions->plans()));
    }

    public function current(Request $request): JsonResponse
    {
        $subscription = $this->subscriptions->current($request->user());

        return $this->ok([
            'is_active' => $subscription !== null,
            'subscription' => $subscription ? new UserSubscriptionResource($subscription) : null,
        ]);
    }
}
