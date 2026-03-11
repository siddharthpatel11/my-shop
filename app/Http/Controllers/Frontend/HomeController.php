<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;

class HomeController extends Controller
{
    /**
     * Show the application home page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get featured/latest products
        $latestProducts = Product::where('status', 'active')
            ->latest()
            ->take(8)
            ->get();

        // Get categories
        $categories = Category::where('status', 'active')
            ->get();

        return view('frontend.home', compact('latestProducts', 'categories'));
    }
}
