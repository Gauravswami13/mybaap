<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\FaqResource;
use App\Http\Resources\PageResource;
use App\Services\ContentService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class ContentController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly ContentService $content) {}

    public function page(string $slug): JsonResponse
    {
        return $this->ok(new PageResource($this->content->page($slug)));
    }

    public function faqs(): JsonResponse
    {
        return $this->ok(FaqResource::collection($this->content->faqs()));
    }
}
