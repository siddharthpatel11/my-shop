<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use Carbon\Carbon;

class ClearOldCartItemsController extends Controller
{
    public function getCart()
    {
        // Step 1: old items delete
        // $cutoff = Carbon::now()->subHours(24);
        $cutoff = Carbon::now()->subMinutes(1);

        CartItem::where('updated_at', '<', $cutoff)->delete();

        // Step 2: latest cart fetch
        $cartItems = CartItem::latest()->get();

        return response()->json([
            'status' => true,
            'message' => 'Cart fetched successfully',
            'data' => $cartItems
        ]);
    }
}
