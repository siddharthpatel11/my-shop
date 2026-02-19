<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Customer\CartItemResource;
use App\Http\Resources\Api\Customer\CartResource;
use App\Models\CartItem;
use App\Models\Discount;
use App\Models\Product;
use App\Models\Tax;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Get all card items + pricing summary
     */
    public function index(Request $request)
    {
        $customerId = $request->user()->id;

        $cartItems = CartItem::with(['product.category', 'color', 'size'])
            ->where('customer_id', $customerId)
            ->get();

        [$subtotal, $taxPercent, $taxAmount, $total] = $this->calcTotals($cartItems);

        return response()->json([
            'success' => true,
            'data'    => new CartResource([
                'cartItems'      => $cartItems,
                'subtotal'       => $subtotal,
                'taxPercent'     => $taxPercent,
                'taxAmount'      => $taxAmount,
                'discountAmount' => 0,
                'discountCode'   => null,
                'total'          => $total,
            ]),
        ]);
    }

    /**
     * Add item to cart
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'color_id'   => 'nullable|exists:colors,id',
            'size_id'    => 'nullable|exists:sizes,id',
            'quantity'   => 'required|integer|min:1|max:10',
        ]);

        $customerId = $request->user()->id;

        $product = Product::where('id', $request->product_id)
            ->where('status', 'active')
            ->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found or unavailable',
            ], 404);
        }

        $price = $product->sle_price ?? $product->price;

        $cartItem = CartItem::where('customer_id', $customerId)
            ->where('product_id', $request->product_id)
            ->where('color_id', $request->color_id)
            ->where('size_id', $request->size_id)
            ->first();

        if ($cartItem) {
            $newQty = $cartItem->quantity + $request->quantity;

            if ($newQty > 10) {
                return response()->json([
                    'success' => false,
                    'message' => 'Maximum quantity per item is 10',
                ], 422);
            }

            $cartItem->update(['quantity' => $newQty]);
            $message = 'Cart item quantity updated';
        } else {
            $cartItem = CartItem::create([
                'customer_id' => $customerId,
                'product_id'  => $request->product_id,
                'color_id'    => $request->color_id,
                'size_id'     => $request->size_id,
                'quantity'    => $request->quantity,
                'price'       => $price,
            ]);
            $message = 'Item added to cart';
        }
        $cartItem->load(['product.category', 'color', 'size']);

        return response()->json([
            'success'   => true,
            'message'   => $message,
            'cart_item' => new CartItemResource($cartItem),
        ], 201);
    }

    /**
     * Update quantity of a cart item
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:10',
        ]);

        $cartItem = CartItem::where('id', $id)
            ->where('customer_id', $request->user()->id)
            ->first();

        if (!$cartItem) {
            return response()->json(['success' => false, 'message' => 'Cart item not found'], 404);
        }

        $cartItem->update(['quantity' => $request->quantity]);
        $cartItem->load(['product.category', 'color', 'size']);

        return response()->json([
            'success'   => true,
            'message'   => 'Cart item updated',
            'cart_item' => new CartItemResource($cartItem),
        ]);
    }

    /**
     * Remove a single cart item
     */

    public function destroy(Request $request, $id)
    {
        $cartItem = CartItem::where('id', $id)
            ->where('customer_id', $request->user()->id)
            ->first();

        if (!$cartItem) {
            return response()->json(['success' => false, 'message' => 'Cart item not found'], 404);
        }

        $cartItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart',
        ]);
    }

    /**
     * Clear all cart items
     */
    public function clear(Request $request)
    {
        CartItem::where('customer_id', $request->user()->id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cart cleared',
        ]);
    }

    /**
     * Validate a discount code and return updated pricing summary
     */
    public function applyDiscount(Request $request)
    {
        $request->validate([
            'discount_code' => 'required|string',
        ]);

        $discount = Discount::where('code', strtoupper($request->discount_code))
            ->valid()
            ->first();

        if (!$discount) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired discount code',
            ], 422);
        }
        $customerId = $request->user()->id;
        $cartItems  = CartItem::where('customer_id', $customerId)->get();

        [$subtotal, $taxPercent, $taxAmount] = $this->calcTotals($cartItems);

        $subtotalWithTax = $subtotal + $taxAmount;
        $discountAmount  = $discount->calculateDiscount($subtotalWithTax);
        $total           = $subtotalWithTax - $discountAmount;

        return response()->json([
            'success' => true,
            'message' => 'Discount applied successfully',
            'data'    => new CartResource([
                'cartItems'      => $cartItems,
                'subtotal'       => $subtotal,
                'taxPercent'     => $taxPercent,
                'taxAmount'      => $taxAmount,
                'discountAmount' => $discountAmount,
                'discountCode'   => $discount->code,
                'total'          => $total,
            ]),
        ]);
    }
    // ── Helpers ───────────────────────────────────────────────────────────────

    private function calcTotals($cartItems): array
    {
        $subtotal = $cartItems->sum(fn($item) => $item->price * $item->quantity);

        $taxRow     = Tax::active()->first();
        $taxPercent = $taxRow ? $taxRow->rate : 0;
        $taxAmount  = ($subtotal * $taxPercent) / 100;

        $total = $subtotal + $taxAmount;

        return [$subtotal, $taxPercent, $taxAmount, $total];
    }
}
