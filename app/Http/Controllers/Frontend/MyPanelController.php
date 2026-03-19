<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MyPanelController extends Controller
{
    public function index()
    {
        $customer = Auth::guard('customer')->user();

        // Get recent orders (last 5)
        $recentOrders = Order::where('customer_id', $customer->id)
            ->latest()
            ->take(5)
            ->get();

        // Get wishlist items (last 5)
        $wishlistItems = Wishlist::with('product')
            ->where('customer_id', $customer->id)
            ->latest()
            ->take(5)
            ->get();

        // Get replied enquiries (messages that admin has replied to)
        $repliedEnquiries = \App\Models\Contact::where('customer_id', $customer->id)
            ->whereNotNull('replied_at')
            ->latest('replied_at')
            ->get();

        $totalOrders = Order::where('customer_id', $customer->id)->count();
        $totalWishlist = Wishlist::where('customer_id', $customer->id)->count();

        return view('frontend.my_panel.index', compact(
            'recentOrders',
            'wishlistItems',
            'repliedEnquiries',
            'totalOrders',
            'totalWishlist'
        ));
    }
}
