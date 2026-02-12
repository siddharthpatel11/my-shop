<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    /**
     * Display the wishlist items for the authenticated customer.
     */
    public function index()
    {
        $customer = Auth::guard('customer')->user();
        $wishlistItems = Wishlist::with('product')->where('customer_id', $customer->id)->latest()->get();

        return view('frontend.wishlist.index', compact('wishlistItems'));
    }

    /**
     * Store a product in the authenticated customer's wishlist.
     */
    public function store(Request $request)
    {
        if (!Auth::guard('customer')->check()) {
            return response()->json(['status' => 'error', 'message' => 'Please login to add to wishlist.'], 401);
        }

        $productId = $request->product_id;
        $customer = Auth::guard('customer')->user();

        // Check if item already exists in wishlist
        $wishlist = Wishlist::where('customer_id', $customer->id)
            ->where('product_id', $productId)
            ->first();

        if ($wishlist) {
            $wishlist->delete();
            return response()->json([
                'status' => 'success',
                'action' => 'removed',
                'message' => 'Product removed from wishlist successfully!'
            ]);
        }

        Wishlist::create([
            'customer_id' => $customer->id,
            'product_id' => $productId,
        ]);

        return response()->json([
            'status' => 'success',
            'action' => 'added',
            'message' => 'Product added to wishlist successfully!'
        ]);
    }

    /**
     * Remove a product from the authenticated customer's wishlist.
     */
    public function destroy($id)
    {
        $customer = Auth::guard('customer')->user();
        $wishlistItem = Wishlist::where('customer_id', $customer->id)->where('id', $id)->first();

        if ($wishlistItem) {
            $wishlistItem->delete();
            return redirect()->back()->with('success', 'Product removed from wishlist.');
        }

        return redirect()->back()->with('error', 'Product not found in wishlist.');
    }
}
