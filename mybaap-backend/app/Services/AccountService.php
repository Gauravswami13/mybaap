<?php

namespace App\Services;

use App\Models\Otp;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Support\Facades\DB;

class AccountService
{
    public function delete(User $user): void
    {
        DB::transaction(function () use ($user) {
            $mobile = $user->mobile;

            $user->tokens()->delete();
            Otp::query()->where('mobile', $mobile)->delete();

            $user->subscriptions()
                ->where('status', UserSubscription::STATUS_ACTIVE)
                ->update(['status' => UserSubscription::STATUS_CANCELLED]);

            $user->forceFill([
                'name' => null,
                'mobile' => 'd'.str_pad((string) $user->id, 14, '0', STR_PAD_LEFT),
                'status' => User::STATUS_DELETED,
            ])->save();

            $user->delete();
        });
    }
}
