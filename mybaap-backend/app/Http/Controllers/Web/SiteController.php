<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Category;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\UserSubscription;
use App\Models\Video;
use App\Services\CatalogService;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SiteController extends Controller
{
    public function __construct(private readonly CatalogService $catalog) {}

    public function home(): View
    {
        $videos = $this->catalog->homeVideos()->map(fn (Video $video) => $this->card($video));
        $banners = $this->catalog->banners()->map(fn (Banner $banner) => $this->banner($banner));
        $categories = $this->catalog->categories()->map(fn (Category $category) => $this->categoryCard($category));

        return view('site.home', [
            'banners' => $banners,
            'categories' => $categories,
            'videos' => $videos,
            'featured' => $videos->first(),
            'paid' => $videos->where('is_paid', true)->values(),
            'free' => $videos->where('is_paid', false)->values(),
            'bottomBanner' => $banners->last(),
            'subscribed' => $this->subscribed(),
        ]);
    }

    public function category(int $id): View
    {
        $category = Category::query()->where('is_active', true)->findOrFail($id);
        $videos = $this->catalog->videos($id)->through(fn (Video $video) => $this->card($video));

        return view('site.category', [
            'category' => $this->categoryCard($category),
            'videos' => $videos,
        ]);
    }

    public function video(int $id): View
    {
        $video = $this->catalog->video($id);
        $card = $this->card($video);
        $related = $this->catalog->homeVideos()
            ->filter(fn (Video $item) => $item->id !== $video->id && $item->category_id === $video->category_id)
            ->take(12)
            ->map(fn (Video $item) => $this->card($item));

        return view('site.video', [
            'video' => $card,
            'related' => $related,
            'subscribed' => $this->subscribed(),
        ]);
    }

    public function plans(): View
    {
        $plans = SubscriptionPlan::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $user = auth()->user();

        return view('site.plans', [
            'plans' => $plans,
            'subscribed' => $this->subscribed(),
            'current' => $user instanceof User ? $this->currentPlanName($user) : null,
        ]);
    }

    private function subscribed(): bool
    {
        $user = auth()->user();

        return $user instanceof User && $user->hasActiveSubscription();
    }

    private function currentPlanName(User $user): ?string
    {
        return $user->subscriptions()
            ->with('plan')
            ->where('status', UserSubscription::STATUS_ACTIVE)
            ->where('end_date', '>=', now()->toDateString())
            ->orderByDesc('end_date')
            ->first()
            ?->plan
            ?->name;
    }

    private function card(Video $video): array
    {
        return [
            'id' => $video->id,
            'title' => $video->title,
            'description' => $video->description,
            'thumbnail' => $this->publicUrl($video->thumbnail),
            'category' => $video->category?->name,
            'category_id' => $video->category_id,
            'duration' => $this->duration($video->duration_seconds),
            'is_paid' => (bool) $video->is_paid,
        ];
    }

    private function banner(Banner $banner): array
    {
        return [
            'title' => $banner->title,
            'image' => $this->publicUrl($banner->image),
        ];
    }

    private function categoryCard(Category $category): array
    {
        return [
            'id' => $category->id,
            'name' => $category->name,
            'image' => $this->publicUrl($category->image),
        ];
    }

    private function publicUrl(?string $path): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }

        return Storage::disk('public')->url($path);
    }

    private function duration(?int $seconds): string
    {
        if ($seconds === null || $seconds < 1) {
            return '';
        }

        $minutes = intdiv($seconds, 60);
        $rest = $seconds % 60;
        if ($minutes >= 60) {
            return intdiv($minutes, 60).'h '.($minutes % 60).'m';
        }

        return $rest > 0 ? $minutes.'m '.$rest.'s' : $minutes.' min';
    }
}
