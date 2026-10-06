<?php

namespace App\Services;

use App\Exceptions\ApiException;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Database\Eloquent\Collection;

class SubscriptionService
{
    public function plans(): Collection
    {
        return SubscriptionPlan::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }

    public function current(User $user): ?UserSubscription
    {
        return $user->activeSubscription()->with('plan')->first();
    }

    public function hasActive(User $user): bool
    {
        return $this->current($user) !== null;
    }

    public function activate(User $user, SubscriptionPlan $plan, ?string $paymentRef): UserSubscription
    {
        if ($paymentRef) {
            $existing = UserSubscription::query()->where('payment_ref', $paymentRef)->first();
            if ($existing) {
                return $existing->load('plan');
            }
        }

        if ($user->isBlocked()) {
            throw new ApiException('Your account is blocked.', 403);
        }

        $current = $this->current($user);
        if ($current) {
            $current->forceFill([
                'plan_id' => $plan->id,
                'end_date' => $current->end_date->copy()->addDays($plan->duration_days)->toDateString(),
                'status' => UserSubscription::STATUS_ACTIVE,
                'payment_ref' => $paymentRef,
            ])->save();

            return $current->load('plan');
        }

        return UserSubscription::query()->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays($plan->duration_days)->toDateString(),
            'status' => UserSubscription::STATUS_ACTIVE,
            'payment_ref' => $paymentRef,
        ])->load('plan');
    }
}
