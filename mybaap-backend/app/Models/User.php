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
        'status',
    ];

    public function subscriptions(): HasMany
    {
        return $this->hasMany(UserSubscription::class);
    }

    public function activeSubscription(): HasOne
    {
        return $this->hasOne(UserSubscription::class)
            ->where('status', UserSubscription::STATUS_ACTIVE)
            ->whereDate('end_date', '>=', now()->toDateString())
            ->latestOfMany('end_date');
    }

    public function hasActiveSubscription(): bool
    {
        return $this->activeSubscription()->exists();
    }

    public function isBlocked(): bool
    {
        return $this->status === self::STATUS_BLOCKED;
    }
}
