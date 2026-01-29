<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\CustomerAddress;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Tax;
use App\Models\Discount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    /**
     * Store customer address
     */
    public function storeAddress(Request $request)
    {
        if (!auth('customer')->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        $request->validate([
            'country' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'pincode' => 'nullable|string|max:10',
            'full_address' => 'nullable|string',
        ]);

        $customerId = auth('customer')->id();

        // If this is the first address, make it default
        $isFirstAddress = !CustomerAddress::where('customer_id', $customerId)->exists();

        $address = CustomerAddress::create([
            'customer_id' => $customerId,
            'country' => $request->country,
            'state' => $request->state,
            'district' => $request->district,
            'city' => $request->city,
            'pincode' => $request->pincode,
            'full_address' => $request->full_address,
            'is_default' => $isFirstAddress,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Address saved successfully',
            'address' => $address
        ]);
    }

    /**
     * Get customer addresses
     */
    public function addresses()
    {
        if (!auth('customer')->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        $addresses = CustomerAddress::where('customer_id', auth('customer')->id())
            ->orderByDesc('is_default')
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'success' => true,
            'addresses' => $addresses
        ]);
    }

    /**
     * Show review page with selected address
     */
    public function review(CustomerAddress $address, Request $request)
    {
        $customer = auth('customer')->user();

        if ($address->customer_id !== $customer->id) {
            abort(403);
        }

        $cartItems = CartItem::with(['product', 'color', 'size'])
            ->where('customer_id', $customer->id)
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('frontend.cart');
        }

        // Subtotal
        $subtotal = $cartItems->sum(fn($item) => $item->price * $item->quantity);

        // Tax calculation on subtotal (before discount)
        $taxRow = Tax::active()->first();
        $taxPercent = $taxRow ? $taxRow->rate : 0;
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

        // Total (Subtotal + Tax - Discount)
        $total = $subtotalWithTax - $discountAmount;

        return view('frontend.cart.review', compact(
            'address',
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
     * Process checkout and create order
     */
    public function processCheckout(Request $request)
    {
        if (!auth('customer')->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        $request->validate([
            'address_id' => 'required|exists:customer_addresses,id',
            'payment_method' => 'nullable|string',
        ]);

        $customerId = auth('customer')->id();

        // Verify address belongs to customer
        $address = CustomerAddress::where('id', $request->address_id)
            ->where('customer_id', $customerId)
            ->first();

        if (!$address) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid address'
            ], 400);
        }

        // Get cart items
        $cartItems = CartItem::with(['product', 'color', 'size'])
            ->where('customer_id', $customerId)
            ->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Your cart is empty'
            ], 400);
        }

        // Calculate totals
        $subtotal = $cartItems->sum(fn($item) => $item->price * $item->quantity);

        // Tax calculation on subtotal (before discount)
        $taxRow = Tax::active()->first();
        $taxId = $taxRow ? $taxRow->id : null;
        $taxPercent = $taxRow ? $taxRow->rate : 0;
        $taxAmount = ($subtotal * $taxPercent) / 100;

        // Subtotal + Tax
        $subtotalWithTax = $subtotal + $taxAmount;

        // Apply discount if exists in session
        $discountAmount = 0;
        $discountCode = null;

        if ($request->session()->has('applied_discount')) {
            $discountCode = $request->session()->get('applied_discount');
            $discount = Discount::where('code', $discountCode)->valid()->first();

            if ($discount) {
                // Apply discount on subtotal + tax
                $discountAmount = $discount->calculateDiscount($subtotalWithTax);
            }
        }

        $shipping = 0;
        // Total (Subtotal + Tax - Discount)
        $total = $subtotalWithTax - $discountAmount + $shipping;

        DB::beginTransaction();
        try {
            // Create order
            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'customer_id' => $customerId,
                'address_id' => $request->address_id,
                'subtotal' => $subtotal,
                'discount' => $discountAmount,
                'discount_code' => $discountCode,
                'tax_id' => $taxId,
                'tax_amount' => $taxAmount,
                'shipping' => $shipping,
                'total' => $total,
                'status' => 'pending',
                'payment_status' => 'pending',
                'payment_method' => $request->payment_method ?? 'cod',
            ]);

            // Create order items from cart
            foreach ($cartItems as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'color_id' => $cartItem->color_id,
                    'size_id' => $cartItem->size_id,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->price,
                    'subtotal' => $cartItem->price * $cartItem->quantity,
                ]);
            }

            // Clear cart and discount session
            CartItem::where('customer_id', $customerId)->delete();
            $request->session()->forget('applied_discount');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully',
                'order' => $order->load('address', 'items.product', 'items.color', 'items.size')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to place order: ' . $e->getMessage()
            ], 500);
        }
    }
}
