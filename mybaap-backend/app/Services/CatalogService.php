<?php

namespace App\Services;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Video;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class CatalogService
{
    public function banners(): Collection
    {
        return Banner::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }

    public function categories(): Collection
    {
        return Category::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }

    public function videos(?int $categoryId = null): LengthAwarePaginator
    {
        return $this->activeVideos($categoryId)
            ->paginate(15)
            ->withQueryString();
    }

    public function homeVideos(): Collection
    {
        return $this->activeVideos()
            ->limit(50)
            ->get();
    }

    public function video(int $id): Video
    {
        $video = $this->activeVideos()->where('videos.id', $id)->first();
        if ($video === null) {
            abort(404, 'Video not found.');
        }

        return $video;
    }

    private function activeVideos(?int $categoryId = null)
    {
        return Video::query()
            ->select('videos.*')
            ->where('videos.is_active', true)
            ->whereHas('category', function ($query) {
                $query->where('is_active', true);
            })
            ->when($categoryId, function ($query) use ($categoryId) {
                $query->where('videos.category_id', $categoryId);
            })
            ->with('category')
            ->orderBy('videos.sort_order')
            ->orderBy('videos.id');
    }
}
