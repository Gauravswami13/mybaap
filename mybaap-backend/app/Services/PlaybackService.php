<?php

namespace App\Services;

use App\Exceptions\ApiException;
use App\Models\User;
use App\Models\Video;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PlaybackService
{
    public function __construct(
        private readonly CatalogService $catalog,
        private readonly SubscriptionService $subscriptions,
    ) {}

    public function signedUrl(User $user, int $videoId): string
    {
        $video = $this->catalog->video($videoId);
        if ($video->is_paid && ! $this->subscriptions->hasActive($user)) {
            throw new ApiException('An active subscription is required to play this video.', 403);
        }

        if (! Storage::disk('local')->exists($video->playback_path)) {
            abort(404, 'Video file is not available.');
        }

        return URL::temporarySignedRoute(
            'videos.stream',
            now()->addMinutes(5),
            ['id' => $video->id],
        );
    }

    public function stream(int $videoId): StreamedResponse
    {
        $video = Video::query()
            ->where('is_active', true)
            ->whereHas('category', function ($query) {
                $query->where('is_active', true);
            })
            ->find($videoId);

        if ($video === null || ! Storage::disk('local')->exists($video->playback_path)) {
            abort(404, 'Video file is not available.');
        }

        return Storage::disk('local')->response($video->playback_path, 'video.mp4', [
            'Content-Type' => 'video/mp4',
            'Cache-Control' => 'private, max-age=60',
        ]);
    }
}
