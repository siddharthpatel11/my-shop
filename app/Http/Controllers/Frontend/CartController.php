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
            $discount = Discount::where('code', $discountCode)->valid()->first();

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

        return view('frontend.cart.index', compact(
            'cartItems',
            'subtotal',
            'discountAmount',
            'appliedDiscount',
            'taxPercent',
            'taxAmount',
            'total'
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

        $discounts = Discount::valid()->get();

        return response()->json([
            'success' => true,
            'discounts' => $discounts
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

        // Find valid discount
        $discount = Discount::where('code', $discountCode)->valid()->first();

        if (!$discount) {
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
        ]);

        $customerId = auth('customer')->id();

        $item = CartItem::where([
            'customer_id' => $customerId,
            'product_id'  => $request->product_id,
            'color_id'    => $request->color_id,
            'size_id'     => $request->size_id,
        ])->first();

        if ($item) {
            $item->quantity = min(10, $item->quantity + 1);
            $item->save();
        } else {
            CartItem::create([
                'customer_id' => $customerId,
                'product_id'  => $request->product_id,
                'color_id'    => $request->color_id,
                'size_id'     => $request->size_id,
                'quantity'    => 1,
                'price'       => $request->price,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Product added to cart'
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

        $request->validate([
            'id' => 'required|integer',
            'quantity' => 'required|min:1|max:10'
        ]);

        CartItem::where('id', $request->id)
            ->where('customer_id', auth('customer')->id())
            ->update(['quantity' => $request->quantity]);

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
