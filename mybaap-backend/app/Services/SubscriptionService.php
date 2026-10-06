<?php

namespace App\Services;

use App\Exceptions\ApiException;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;

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
                if ($existing->user_id !== $user->id) {
                    throw new ApiException('Payment reference is already used.', 409);
                }

                return $existing->load('plan');
            }
        }

        if ($user->isBlocked()) {
            throw new ApiException('Your account is blocked.', 403);
        }

        $current = $this->current($user);
        try {
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
        } catch (QueryException $exception) {
            if ((int) ($exception->errorInfo[1] ?? 0) === 1062 || str_contains($exception->getMessage(), 'UNIQUE')) {
                throw new ApiException('Payment reference is already used.', 409);
            }

            throw $exception;
        }
    }

    public function manualActivate(UserSubscription $subscription): void
    {
        $days = (int) ($subscription->plan?->duration_days ?? 30);
        if ($subscription->end_date === null || $subscription->end_date->lt(today())) {
            $subscription->start_date = today();
            $subscription->end_date = today()->addDays($days);
        }
        $subscription->status = UserSubscription::STATUS_ACTIVE;
        $subscription->save();
    }

    public function extend(UserSubscription $subscription): void
    {
        $days = (int) ($subscription->plan?->duration_days ?? 30);
        $base = $subscription->end_date && $subscription->end_date->gte(today())
            ? $subscription->end_date->copy()
            : today();

        $subscription->forceFill([
            'status' => UserSubscription::STATUS_ACTIVE,
            'start_date' => $subscription->start_date ?? today()->toDateString(),
            'end_date' => $base->addDays($days)->toDateString(),
        ])->save();
    }

    public function cancel(UserSubscription $subscription): void
    {
        $subscription->update([
            'status' => UserSubscription::STATUS_CANCELLED,
        ]);
    }
}
