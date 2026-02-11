<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Page;

class PageController extends Controller
{
    /**
     * Display the specified page.
     */
    public function show($slug)
    {
        $page = Page::where('slug', $slug)->firstOrFail();

        // Check if a custom view exists for this slug
        $viewName = "frontend.pages.{$page->slug}";

        if (view()->exists($viewName)) {
            return view($viewName, compact('page'));
        }

        return view('frontend.pages.show', compact('page'));
    }
}
