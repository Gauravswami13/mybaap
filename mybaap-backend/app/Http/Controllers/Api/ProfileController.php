<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserSubscriptionResource;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
        $user->name = $request->string('name')->toString();
        if ($request->hasFile('photo')) {
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }
            $user->photo = $request->file('photo')->store('profiles', 'public');
        }
        $user->save();
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
