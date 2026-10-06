<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\UserSubscription */
class UserSubscriptionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'plan_id' => $this->plan_id,
            'plan_name' => $this->plan?->name,
            'price' => $this->plan?->price,
            'start_date' => $this->start_date?->toDateString(),
            'end_date' => $this->end_date?->toDateString(),
            'status' => $this->status,
            'payment_ref' => $this->payment_ref,
            'created_at' => $this->created_at?->toIso8601String(),
            'is_active' => $this->isCurrentlyActive(),
        ];
    }
}
