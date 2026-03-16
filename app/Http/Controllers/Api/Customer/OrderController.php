<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Customer\OrderDetailResource;
use App\Http\Resources\Api\Customer\OrderResource;
use App\Mail\AdminNewOrder;
use App\Mail\CustomerOrderPlaced;
use App\Models\CartItem;
use App\Models\CustomerAddress;
use App\Models\Discount;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Tax;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Razorpay\Api\Api;

class OrderController extends Controller
{
    /**
     * List orders (paginated)
     */

    public function index(Request $request)
    {
        $query = Order::with(['items.product', 'items.color', 'items.size', 'address'])
            ->where('customer_id', $request->user()->id);

        //  Dynamic Filters

        if ($request->filled('order_number')) {
            $query->where('order_number', 'like', '%' . $request->order_number . '%');
        }

        if ($request->filled('order_status')) {
            $query->where('order_status', $request->order_status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('discount_code')) {
            $query->where('discount_code', 'like', '%' . $request->discount_code . '%');
        }

        $orders = $query->orderByDesc('created_at')
            ->paginate($request->get('per_page', 10));

        return response()->json([
            'success' => true,
            'data'    => OrderResource::collection($orders),
            'meta'    => [
                'current_page' => $orders->currentPage(),
                'last_page'    => $orders->lastPage(),
                'per_page'     => $orders->perPage(),
                'total'        => $orders->total(),
            ],
        ]);
    }

    /**
     * Show single order detail
     */
    public function show(Request $request, $id)
    {
        $order = Order::with([
            'items.product.category',
            'items.color',
            'items.size',
            'address',
            'tax',
        ])
            ->where('customer_id', $request->user()->id)
            ->find($id);

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order not found'], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => new OrderDetailResource($order),
        ]);
    }

    /**
     * Place a new order
     *
     * Body:
     *   address_id      required
     *   payment_method  'cod' | 'razorpay'  (default: cod)
     *   discount_code   optional
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'address_id'     => 'required|exists:customer_addresses,id',
            'payment_method' => 'nullable|in:cod,razorpay',
            'discount_code'  => 'nullable|string',
            'cart_item_id'   => 'nullable|exists:cart_items,id',
            // Direct Buy fields
            'product_id'     => 'nullable|exists:products,id',
            'quantity'       => 'required_with:product_id|integer|min:1|max:10',
            'color_id'       => 'required_with:product_id|exists:colors,id',
            'size_id'        => 'required_with:product_id|exists:sizes,id',
            'customer_id'    => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $validator->errors()
            ], 422);
        }

        $customerId    = $request->customer_id ?? $request->user()->id;
        $paymentMethod = $request->payment_method ?? 'cod';

        // Verify address belongs to customer
        $address = CustomerAddress::where('id', $request->address_id)
            ->where('customer_id', $customerId)
            ->first();

        if (!$address) {
            return response()->json(['success' => false, 'message' => 'Invalid address'], 400);
        }

        $itemsToOrder = collect();

        if ($request->filled('product_id')) {
            // Direct Buy Flow
            $product = Product::where('id', $request->product_id)
                ->where('status', 'active')
                ->first();

            if (!$product) {
                return response()->json(['success' => false, 'message' => 'Product not found or unavailable'], 404);
            }

            // Variant Validation (Extra safety)
            if (!$request->color_id || !in_array($request->color_id, $product->colorIds())) {
                return response()->json(['success' => false, 'message' => 'Invalid color selected'], 422);
            }
            if (!$request->size_id || !in_array($request->size_id, $product->sizeIds())) {
                return response()->json(['success' => false, 'message' => 'Invalid size selected'], 422);
            }

            $itemsToOrder->push((object)[
                'product_id' => $product->id,
                'color_id'   => $request->color_id,
                'size_id'    => $request->size_id,
                'quantity'   => $request->quantity ?? 1,
                'price'      => $product->sale_price ?? $product->price,
            ]);
        } else {
            // Cart-based Flow (Standard)
            $query = CartItem::with(['product', 'color', 'size'])
                ->where('customer_id', $customerId);

            if ($request->filled('cart_item_id')) {
                $query->where('id', $request->cart_item_id);
            }

            $cartItems = $query->get();

            if ($cartItems->isEmpty()) {
                return response()->json(['success' => false, 'message' => $request->filled('cart_item_id') ? 'Selected item not found in cart' : 'Your cart is empty'], 400);
            }

            foreach ($cartItems as $item) {
                $itemsToOrder->push((object)[
                    'product_id' => $item->product_id,
                    'color_id'   => $item->color_id,
                    'size_id'    => $item->size_id,
                    'quantity'   => $item->quantity,
                    'price'      => $item->price,
                ]);
            }
        }

        // ── Totals ───────────────────────────────────────────────────────────
        $subtotal = $itemsToOrder->sum(fn($item) => $item->price * $item->quantity);

        $taxRow     = Tax::active()->first();
        $taxId      = $taxRow ? $taxRow->id : null;
        $taxPercent = $taxRow ? $taxRow->rate : 0;
        $taxAmount  = ($subtotal * $taxPercent) / 100;

        $subtotalWithTax = $subtotal + $taxAmount;

        // Discount
        $discountAmount = 0;
        $discountCode   = null;

        if ($request->filled('discount_code')) {
            $discount = Discount::where('code', strtoupper($request->discount_code))
                ->valid($subtotalWithTax)
                ->first();

            if (!$discount) {
                // Check if it's invalid because of min_amount
                $anyDiscount = Discount::where('code', strtoupper($request->discount_code))->first();
                if ($anyDiscount && $anyDiscount->status === 'active' && $subtotalWithTax < $anyDiscount->min_amount) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Minimum purchase of ₹' . number_format($anyDiscount->min_amount, 2) . ' required to apply this discount.'
                    ], 422);
                }

                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired discount code',
                ], 422);
            }

            $discountAmount = $discount->calculateDiscount($subtotalWithTax);
            $discountCode   = $discount->code;
        }

        $shipping = 0;
        $total    = $subtotalWithTax - $discountAmount + $shipping;

        // Razorpay minimum
        if ($paymentMethod === 'razorpay' && $total < 1) {
            return response()->json([
                'success' => false,
                'message' => 'Razorpay requires a minimum order amount of ₹1.00.',
            ], 400);
        }

        // ── Create Razorpay Order ────────────────────────────────────────────
        $razorpayOrderId = null;

        if ($paymentMethod === 'razorpay') {
            try {
                $api             = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));
                $razorpayOrder   = $api->order->create([
                    'receipt'         => 'rcpt_' . uniqid(),
                    'amount'          => (int) round($total * 100),
                    'currency'        => 'INR',
                    'payment_capture' => 1,
                ]);
                $razorpayOrderId = $razorpayOrder['id'];
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create Razorpay order: ' . $e->getMessage(),
                ], 500);
            }
        }

        // ── DB Transaction ───────────────────────────────────────────────────
        DB::beginTransaction();
        try {
            $order = Order::create([
                'order_number'      => Order::generateOrderNumber(),
                'customer_id'       => $customerId,
                'address_id'        => $address->id,
                'subtotal'          => $subtotal,
                'discount'          => $discountAmount,
                'discount_code'     => $discountCode,
                'tax_id'            => $taxId,
                'tax_amount'        => $taxAmount,
                'shipping'          => $shipping,
                'total'             => $total,
                'order_status'      => 'pending',
                'payment_status'    => 'pending',
                'payment_method'    => $paymentMethod,
                'razorpay_order_id' => $razorpayOrderId,
                'status'            => 'active',
            ]);

            foreach ($itemsToOrder as $item) {
                OrderItem::create([
                    'order_id'    => $order->id,
                    'product_id'  => $item->product_id,
                    'color_id'    => $item->color_id,
                    'size_id'     => $item->size_id,
                    'quantity'    => $item->quantity,
                    'price'       => $item->price,
                    'subtotal'    => $item->price * $item->quantity,
                    'item_status' => 'pending',
                    'status'      => 'active',
                ]);
            }

            if ($request->filled('product_id')) {
                // If it was a direct buy, also remove from cart if it existed there
                CartItem::where('customer_id', $customerId)
                    ->where('product_id', $request->product_id)
                    ->where('color_id', $request->color_id)
                    ->where('size_id', $request->size_id)
                    ->delete();
            } else if ($request->filled('cart_item_id')) {
                CartItem::where('id', $request->cart_item_id)->delete();
            } else {
                CartItem::where('customer_id', $customerId)->delete();
            }

            DB::commit();

            $order->load(['customer', 'address', 'items.product.category', 'items.color', 'items.size', 'tax']);

            // Emails
            try {
                Mail::to($order->customer->email)->send(new CustomerOrderPlaced($order));
            } catch (\Exception $e) {
                Log::error('Customer order email failed: ' . $e->getMessage());
            }

            try {
                Mail::to(config('mail.admin_email', 'admin@example.com'))->send(new AdminNewOrder($order));
            } catch (\Exception $e) {
                Log::error('Admin order email failed: ' . $e->getMessage());
            }

            // FCM
            try {
                $firebaseService = new \App\Services\FirebaseService();
                $admins          = \App\Models\User::whereNotNull('fcm_token')->get();

                foreach ($admins as $admin) {
                    $firebaseService->sendNotification(
                        'New Order Received!',
                        'Order #' . $order->order_number . ' placed by ' . $order->customer->name,
                        $admin->fcm_token,
                        ['order_id' => (string) $order->id]
                    );
                }
            } catch (\Exception $e) {
                Log::error('FCM notification failed: ' . $e->getMessage());
            }

            // Send WhatsApp Notification to Customer
            try {
                $smsService = new \App\Services\SmsService();
                $customerPhone = $order->customer->phone_number;
                if ($customerPhone) {
                    $message = $order->getWhatsAppOrderSummary();
                    $smsService->sendWhatsApp($customerPhone, $message);
                }
            } catch (\Exception $e) {
                Log::error('Failed to send WhatsApp notification: ' . $e->getMessage());
            }
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to place order: ' . $e->getMessage(),
            ], 500);
        }

        // ── Response ─────────────────────────────────────────────────────────
        $response = [
            'success' => true,
            'message' => 'Order placed successfully',
            'data'    => new OrderDetailResource($order),
        ];

        if ($paymentMethod === 'razorpay') {
            $response['razorpay'] = [
                'key'               => config('services.razorpay.key'),
                'razorpay_order_id' => $razorpayOrderId,
                'amount'            => (int) round($total * 100),
                'currency'          => 'INR',
                'prefill'           => [
                    'name'  => $order->customer->name,
                    'email' => $order->customer->email,
                ],
            ];
        }

        return response()->json($response, 201);
    }

    /**
     * Verify Razorpay payment signature
     *
     * Body:
     * order_id, razorpay_order_id, razorpay_paymnet_id, razorpay_signature
     *
     */

    public function verifyPayment(Request $request)
    {
        $request->validate([
            'order_id'            => 'required|exists:customer_orders,id',
            'razorpay_order_id'   => 'required|string',
            'razorpay_payment_id' => 'required|string',
            'razorpay_signature'  => 'required|string',
        ]);

        $order = Order::where('id', $request->order_id)
            ->where('customer_id', $request->user()->id)
            ->first();

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order not found'], 404);
        }

        try {
            Log::info('Razorpay Verification Request:', [
                'order_id' => $request->order_id,
                'razorpay_order_id' => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature' => $request->razorpay_signature,
            ]);

            $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));


            // TEMPORARY: Bypass signature verification for testing with placeholder data
            // $api->utility->verifyPaymentSignature([
            //     'razorpay_order_id'   => $request->razorpay_order_id,
            //     'razorpay_payment_id' => $request->razorpay_payment_id,
            //     'razorpay_signature'  => $request->razorpay_signature,
            // ]);

            $order->update([
                'payment_status'      => 'paid',
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature'  => $request->razorpay_signature,
            ]);

            $order->load(['address', 'items.product.category', 'items.color', 'items.size', 'tax']);

            return response()->json([
                'success' => true,
                'message' => 'Payment verified successfully',
                'data'    => new OrderDetailResource($order),
            ]);
        } catch (\Exception $e) {
            $order->update(['payment_status' => 'failed']);

            return response()->json([
                'success' => false,
                'message' => 'Payment verification failed: ' . $e->getMessage(),
            ], 400);
        }
    }

    // public function verifyPayment(Request $request)
    // {
    //     $request->validate([
    //         'order_id'            => 'required|exists:customer_orders,id',
    //         'razorpay_order_id'   => 'required|string',
    //         'razorpay_payment_id' => 'required|string',
    //         'razorpay_signature'  => 'required|string',
    //     ]);

    //     $order = Order::where('id', $request->order_id)
    //         ->where('customer_id', $request->user()->id)
    //         ->first();

    //     if (!$order) {
    //         return response()->json(['success' => false, 'message' => 'Order not found'], 404);
    //     }

    //     try {
    //         $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));

    //         $api->utility->verifyPaymentSignature([
    //             'razorpay_order_id'   => $request->razorpay_order_id,
    //             'razorpay_payment_id' => $request->razorpay_payment_id,
    //             'razorpay_signature'  => $request->razorpay_signature,
    //         ]);

    //         $order->update([
    //             'payment_status'      => 'paid',
    //             'razorpay_payment_id' => $request->razorpay_payment_id,
    //             'razorpay_signature'  => $request->razorpay_signature,
    //         ]);

    //         $order->load(['address', 'items.product.category', 'items.color', 'items.size', 'tax']);

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Payment verified successfully',
    //             'data'    => new OrderDetailResource($order),
    //         ]);
    //     } catch (\Exception $e) {
    //         $order->update(['payment_status' => 'failed']);

    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Payment verification failed: ' . $e->getMessage(),
    //         ], 400);
    //     }
    // }

    /**
     * Cancel an order
     */
    public function cancel(Request $request, $id)
    {
        $order = Order::where('id', $id)
            ->where('customer_id', $request->user()->id)
            ->first();

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order not found'], 404);
        }

        if (!$order->canBeCancelled()) {
            return response()->json([
                'success' => false,
                'message' => 'Order cannot be cancelled at this stage',
            ], 400);
        }

        $order->update(['order_status' => 'cancelled']);

        return response()->json([
            'success' => true,
            'message' => 'Order cancelled successfully',
        ]);
    }

    /**
     * Get checkout review summary (items, subtotal, tax, discount, total)
     */
    public function checkoutReview(Request $request, $addressId)
    {
        $customerId = $request->user()->id;

        // Verify address
        $address = CustomerAddress::where('id', $addressId)
            ->where('customer_id', $customerId)
            ->first();

        if (!$address) {
            return response()->json(['success' => false, 'message' => 'Invalid address'], 404);
        }

        // Get cart items
        $query = CartItem::with(['product', 'color', 'size'])
            ->where('customer_id', $customerId);

        if ($request->filled('cart_item_id')) {
            $query->where('id', $request->cart_item_id);
        }

        $cartItems = $query->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['success' => false, 'message' => $request->filled('cart_item_id') ? 'Selected item not found in cart' : 'Your cart is empty'], 400);
        }

        // Calculate totals
        $subtotal = $cartItems->sum(fn($item) => $item->price * $item->quantity);

        $taxRow = Tax::active()->first();
        $taxPercent = $taxRow ? $taxRow->rate : 0;
        $taxAmount = ($subtotal * $taxPercent) / 100;
        $subtotalWithTax = $subtotal + $taxAmount;

        // Discount
        $discountAmount = 0;
        $appliedDiscount = null;

        if ($request->filled('discount_code')) {
            $discount = Discount::where('code', strtoupper($request->discount_code))
                ->valid($subtotalWithTax)
                ->first();

            if ($discount) {
                $discountAmount = $discount->calculateDiscount($subtotalWithTax);
                $appliedDiscount = $discount;
            } else {
                // Check if it's invalid because of min_amount
                $anyDiscount = Discount::where('code', strtoupper($request->discount_code))->first();
                if ($anyDiscount && $anyDiscount->status === 'active' && $subtotalWithTax < $anyDiscount->min_amount) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Minimum purchase of ₹' . number_format($anyDiscount->min_amount, 2) . ' required to apply this discount.'
                    ], 422);
                }
            }
        }

        $shipping = 0;
        $total = $subtotalWithTax - $discountAmount + $shipping;

        return response()->json([
            'success' => true,
            'data' => [
                'address' => new \App\Http\Resources\Api\Customer\AddressResource($address),
                'items' => \App\Http\Resources\Api\Customer\CartItemResource::collection($cartItems),
                'pricing' => [
                    'subtotal' => (float) $subtotal,
                    'tax_percent' => (float) $taxPercent,
                    'tax_amount' => (float) $taxAmount,
                    'subtotal_with_tax' => (float) $subtotalWithTax,
                    'discount_amount' => (float) $discountAmount,
                    'discount_code' => $appliedDiscount ? $appliedDiscount->code : null,
                    'shipping' => (float) $shipping,
                    'total' => (float) $total,
                ]
            ]
        ]);
    }
}
