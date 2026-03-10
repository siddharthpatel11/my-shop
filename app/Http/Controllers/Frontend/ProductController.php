<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Size;
use App\Models\Color;
use App\Models\Wishlist;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        $products = Product::with(['category'])
            ->where('status', 'active')
            ->orderBy('created_at', 'asc')
            ->paginate(12);

        $sizes = Size::all();
        $colors = Color::all();
        $categories = Category::where('status', 'active')->get();

        $wishlistProductIds = [];
        $cartProductIds = [];
        if (Auth::guard('customer')->check()) {
            $customerId = Auth::guard('customer')->id();
            $wishlistProductIds = Wishlist::where('customer_id', $customerId)
                ->pluck('product_id')
                ->toArray();
            $cartProductIds = CartItem::where('customer_id', $customerId)
                ->pluck('product_id')
                ->unique()
                ->toArray();
        }

        return view('frontend.products.index', compact('products', 'sizes', 'colors', 'categories', 'wishlistProductIds', 'cartProductIds'));
    }

    public function show(Product $product): View
    {
        $product->load(['category']);

        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('status', 'active')
            ->take(4)
            ->get();

        $sizes = Size::all();
        $colors = Color::all();

        $inWishlist = false;
        $wishlistProductIds = [];
        $cartProductIds = [];
        if (Auth::guard('customer')->check()) {
            $customerId = Auth::guard('customer')->id();
            $wishlistProductIds = Wishlist::where('customer_id', $customerId)
                ->pluck('product_id')
                ->toArray();
            $inWishlist = in_array($product->id, $wishlistProductIds);

            $cartProductIds = CartItem::where('customer_id', $customerId)
                ->pluck('product_id')
                ->unique()
                ->toArray();
        }

        return view('frontend.products.show', compact('product', 'relatedProducts', 'sizes', 'colors', 'inWishlist', 'wishlistProductIds', 'cartProductIds'));
    }
}
