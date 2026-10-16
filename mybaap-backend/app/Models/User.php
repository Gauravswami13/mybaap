<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, SoftDeletes;

    public const STATUS_ACTIVE = 'active';

    public const STATUS_BLOCKED = 'blocked';

    public const STATUS_DELETED = 'deleted';

    protected $fillable = [
        'mobile',
        'name',
        'photo',
        'status',
    ];

    public function subscriptions(): HasMany
    {
        return $this->hasMany(UserSubscription::class);
    }

    public function latestSubscription(): HasOne
    {
        return $this->hasOne(UserSubscription::class)->latestOfMany();
    }

    public function activeSubscription(): HasOne
    {
        return $this->hasOne(UserSubscription::class)->ofMany(
            ['end_date' => 'max', 'id' => 'max'],
            function ($query) {
                $query->where('status', UserSubscription::STATUS_ACTIVE)
                    ->where('end_date', '>=', now()->toDateString());
            },
        );
    }

    public function hasActiveSubscription(): bool
    {
        return $this->subscriptions()
            ->where('status', UserSubscription::STATUS_ACTIVE)
            ->where('end_date', '>=', now()->toDateString())
            ->exists();
    }

    public function isBlocked(): bool
    {
        return $this->status === self::STATUS_BLOCKED;
    }
}
