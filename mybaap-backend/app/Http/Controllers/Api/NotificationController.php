<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $items = AppNotification::query()
            ->where(function ($query) use ($userId): void {
                $query->where('audience', 'all')
                    ->orWhere(function ($query) use ($userId): void {
                        $query->where('audience', 'one')->where('user_id', $userId);
                    });
            })
            ->latest()
            ->limit(50)
            ->get()
            ->map(fn (AppNotification $item): array => [
                'id' => $item->id,
                'title' => $item->title,
                'body' => $item->body,
                'created_at' => $item->created_at?->toIso8601String(),
            ])
            ->values();

        return $this->ok($items);
    }
}
