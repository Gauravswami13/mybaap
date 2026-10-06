<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ListVideosRequest;
use App\Http\Resources\BannerResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\VideoResource;
use App\Services\CatalogService;
use App\Services\SubscriptionService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly CatalogService $catalog,
        private readonly SubscriptionService $subscriptions,
    ) {}

    public function home(Request $request): JsonResponse
    {
        $this->rememberSubscription($request);

        return $this->ok([
            'banners' => BannerResource::collection($this->catalog->banners()),
            'categories' => CategoryResource::collection($this->catalog->categories()),
            'videos' => VideoResource::collection($this->catalog->homeVideos()),
        ]);
    }

    public function categories(): JsonResponse
    {
        return $this->ok(CategoryResource::collection($this->catalog->categories()));
    }

    public function videos(ListVideosRequest $request): JsonResponse
    {
        $this->rememberSubscription($request);
        $categoryId = $request->filled('category_id') ? $request->integer('category_id') : null;
        $page = $this->catalog->videos($categoryId);

        return $this->ok([
            'videos' => VideoResource::collection($page->items()),
            'pagination' => [
                'current_page' => $page->currentPage(),
                'last_page' => $page->lastPage(),
                'per_page' => $page->perPage(),
                'total' => $page->total(),
            ],
        ]);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $this->rememberSubscription($request);

        return $this->ok(new VideoResource($this->catalog->video($id)));
    }

    private function rememberSubscription(Request $request): void
    {
        $request->attributes->set(
            'subscribed',
            $this->subscriptions->hasActive($request->user()),
        );
    }
}
