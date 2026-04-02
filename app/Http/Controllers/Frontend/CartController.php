<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Discount;
use App\Models\Tax;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    /**
     * Display the shopping cart page
     */
    public function index(Request $request)
    {
        if (!auth('customer')->check()) {
            return redirect()->route('customer.login');
        }

        $cartItems = CartItem::with(['product', 'color', 'size'])
            ->where('customer_id', auth('customer')->id())
            ->get();

        // Subtotal
        $subtotal = $cartItems->sum(fn($item) => $item->price * $item->quantity);

        // Tax calculation on subtotal (before discount)
        $tax = Tax::active()->first();
        $taxPercent = $tax ? $tax->rate : 0;
        $taxAmount = ($subtotal * $taxPercent) / 100;

        // Subtotal + Tax
        $subtotalWithTax = $subtotal + $taxAmount;

        // Initialize discount
        $discountAmount = 0;
        $appliedDiscount = null;

        // Check if discount is applied in session
        if ($request->session()->has('applied_discount')) {
            $discountCode = $request->session()->get('applied_discount');
            $discount = Discount::where('code', $discountCode)->valid($subtotalWithTax)->first();

            if ($discount) {
                // Apply discount on subtotal + tax
                $discountAmount = $discount->calculateDiscount($subtotalWithTax);
                $appliedDiscount = $discount;
            } else {
                // Remove invalid discount from session
                $request->session()->forget('applied_discount');
            }
        }

        // Final total (Subtotal + Tax - Discount)
        $total = $subtotalWithTax - $discountAmount;

        // Default Address
        $defaultAddress = \App\Models\CustomerAddress::where('customer_id', auth('customer')->id())
            ->where('is_default', 1)
            ->first();

        return view('frontend.cart.index', compact(
            'cartItems',
            'subtotal',
            'discountAmount',
            'appliedDiscount',
            'taxPercent',
            'taxAmount',
            'total',
            'defaultAddress'
        ));
    }

    /**
     * Get valid discounts
     */
    public function getValidDiscounts()
    {
        if (!auth('customer')->check()) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $cartItems = CartItem::where('customer_id', auth('customer')->id())->get();
        $subtotal = $cartItems->sum(fn($item) => $item->price * $item->quantity);

        $tax = Tax::active()->first();
        $taxPercent = $tax ? $tax->rate : 0;
        $taxAmount = ($subtotal * $taxPercent) / 100;
        $subtotalWithTax = $subtotal + $taxAmount;

        // Get all active discounts that are within valid date range
        $discounts = Discount::active()
            ->where(function ($q) {
                $now = now();
                $q->where(function ($sq) use ($now) {
                    $sq->whereNull('start_date')->orWhere('start_date', '<=', $now);
                })->where(function ($sq) use ($now) {
                    $sq->whereNull('end_date')->orWhere('end_date', '>=', $now);
                });
            })
            ->get();

        return response()->json([
            'success' => true,
            'discounts' => $discounts,
            'subtotalWithTax' => $subtotalWithTax
        ]);
    }

    /**
     * Apply discount code
     */

    public function applyDiscount(Request $request)
    {
        if (!auth('customer')->check()) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $request->validate([
            'discount_code' => 'required|string'
        ]);

        $discountCode = strtoupper(trim($request->discount_code));

        // Get subtotal for validation
        $cartItems = CartItem::where('customer_id', auth('customer')->id())->get();
        $subtotal = $cartItems->sum(fn($item) => $item->price * $item->quantity);

        $tax = Tax::active()->first();
        $taxPercent = $tax ? $tax->rate : 0;
        $taxAmount = ($subtotal * $taxPercent) / 100;
        $subtotalWithTax = $subtotal + $taxAmount;

        // Find valid discount
        $discount = Discount::where('code', $discountCode)->valid($subtotalWithTax)->first();

        if (!$discount) {
            // Check if it's invalid because of min_amount
            $anyDiscount = Discount::where('code', $discountCode)->first();
            if ($anyDiscount && $anyDiscount->status === 'active' && $subtotalWithTax < $anyDiscount->min_amount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Minimum purchase of ₹' . number_format($anyDiscount->min_amount, 2) . ' required to apply this discount.'
                ], 422);
            }

            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired discount code'
            ], 422);
        }

        // Store discount in session
        $request->session()->put('applied_discount', $discount->code);

        return response()->json([
            'success' => true,
            'message' => 'Discount applied successfully',
            'discount' => $discount
        ]);
    }

    public function removeDiscount(Request $request)
    {
        if (!auth('customer')->check()) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $request->session()->forget('applied_discount');

        return response()->json([
            'success' => true,
            'message' => 'Discount removed'
        ]);
    }

    // Add to Cart
    public function add(Request $request)
    {
        if (!auth('customer')->check()) {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|min:1|max:10',
            'price'      => 'required|numeric',
            'color_id'   => 'nullable|exists:colors,id',
            'size_id'    => 'nullable|exists:sizes,id',
            'mode'       => 'nullable|string|in:increment,replace',
        ]);

        $productId = $request->product_id;
        $quantity = $request->quantity;
        $customerId = auth('customer')->id();
        $mode = $request->input('mode', 'increment');

        $product = \App\Models\Product::find($productId);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $item = CartItem::where([
            'customer_id' => $customerId,
            'product_id'  => $productId,
            'color_id'    => $request->color_id,
            'size_id'     => $request->size_id,
        ])->first();

        // Calculate total requested quantity
        $newQuantity = $quantity;
        if ($item && $mode === 'increment') {
            $newQuantity += $item->quantity;
        }

        // Validate stock
        if (!$product->hasStock($newQuantity)) {
            $available = $product->stock;
            return response()->json([
                'success' => false,
                'message' => "Only {$available} items available in stock. You already have " . ($item ? $item->quantity : 0) . " in your cart."
            ], 422);
        }

        if ($item) {
            if ($mode === 'replace') {
                $item->quantity = $quantity;
            } else {
                $item->quantity = $newQuantity;
            }
            $item->save();
        } else {
            $item = CartItem::create([
                'customer_id' => $customerId,
                'product_id'  => $productId,
                'color_id'    => $request->color_id,
                'size_id'     => $request->size_id,
                'quantity'    => $quantity,
                'price'       => $request->price,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Product added to cart',
            'cart_item_id' => $item->id,
        ]);
    }

    // Update Quantity
    public function update(Request $request)
    {
        if (!auth('customer')->check()) {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }

        $cartId = $request->id;
        $quantity = $request->quantity;

        $cartItem = CartItem::with('product')->where('id', $cartId)
            ->where('customer_id', auth('customer')->id())
            ->first();

        if (!$cartItem) {
            return response()->json(['success' => false, 'message' => 'Cart item not found'], 404);
        }

        // Validate stock
        if (!$cartItem->product->hasStock($quantity)) {
            return response()->json([
                'success' => false,
                'message' => "Only {$cartItem->product->stock} items available in stock."
            ], 422);
        }

        $cartItem->update(['quantity' => $quantity]);

        return response()->json(['success' => true]);
    }

    // Remove Item
    public function remove($id)
    {
        if (!auth('customer')->check()) {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }

        CartItem::where('id', $id)
            ->where('customer_id', auth('customer')->id())
            ->delete();

        return response()->json(['success' => true]);
    }

    // Clear Cart
    public function clear()
    {
        if (!auth('customer')->check()) {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }

        CartItem::where('customer_id', auth('customer')->id())->delete();
        return response()->json(['success' => true]);
    }
}
