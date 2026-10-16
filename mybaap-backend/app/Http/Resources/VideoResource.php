<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/** @mixin \App\Models\Video */
class VideoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        if (! $request->attributes->has('subscribed')) {
            $user = $request->user();
            $request->attributes->set(
                'subscribed',
                $user instanceof User && $user->hasActiveSubscription(),
            );
        }
        $subscribed = (bool) $request->attributes->get('subscribed');

        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'thumbnail_url' => $this->thumbnail ? Storage::disk('public')->url($this->thumbnail) : null,
            'category' => $this->category ? [
                'id' => $this->category->id,
                'name' => $this->category->name,
                'slug' => $this->category->slug,
            ] : null,
            'duration' => $this->duration_seconds,
            'is_paid' => $this->is_paid,
            'locked' => $this->is_paid && ! $subscribed,
        ];
    }
}
