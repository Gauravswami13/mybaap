<?php

namespace App\Http\Controllers;

use App\Services\ContentService;
use Illuminate\View\View;

class PageController extends Controller
{
    public function __construct(private readonly ContentService $content) {}

    public function show(string $slug): View
    {
        return view('pages.show', [
            'page' => $this->content->page($slug),
        ]);
    }
}
