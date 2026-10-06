<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PlaybackService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PlaybackController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly PlaybackService $playback) {}

    public function show(Request $request, int $id): JsonResponse
    {
        return $this->ok([
            'url' => $this->playback->signedUrl($request->user(), $id),
            'expires_in' => 300,
        ], 'Playback URL ready.');
    }

    public function stream(int $id): StreamedResponse
    {
        return $this->playback->stream($id);
    }
}
