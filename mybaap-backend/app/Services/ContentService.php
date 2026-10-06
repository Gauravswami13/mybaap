<?php

namespace App\Services;

use App\Models\Faq;
use App\Models\Page;
use Illuminate\Database\Eloquent\Collection;

class ContentService
{
    public function page(string $slug): Page
    {
        $page = Page::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->first();

        if ($page === null) {
            abort(404, 'Page not found.');
        }

        return $page;
    }

    public function faqs(): Collection
    {
        return Faq::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }
}
