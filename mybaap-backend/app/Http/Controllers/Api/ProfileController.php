<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserSubscriptionResource;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    use ApiResponse;

    public function show(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->load('activeSubscription.plan');

        return $this->ok($this->payload($user), 'OK');
    }

    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user = $request->user();
        $user->update([
            'name' => $request->string('name')->toString(),
        ]);
        $user->load('activeSubscription.plan');

        return $this->ok($this->payload($user), 'Profile updated.');
    }

    private function payload($user): array
    {
        $subscription = $user->activeSubscription;

        return [
            'user' => new UserResource($user),
            'subscription' => $subscription ? new UserSubscriptionResource($subscription) : null,
        ];
    }
}
