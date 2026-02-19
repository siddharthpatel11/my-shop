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
use App\Models\Tax;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use phpDocumentor\Reflection\Types\Nullable;
use Razorpay\Api\Api;

class OrderController extends Controller
{
    /**
     * List orders (paginated)
     */

    public function index(Request $request)
    {
        $orders = Order::with(['items.product', 'items.color', 'items.size', 'address'])
            ->where('customer_id', $request->user()->id)
            ->orderByDesc('created_at')
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
        $request->validate([
            'address_id'     => 'required|exists:customer_addresses,id',
            'payment_method' => 'nullable|in:cod,razorpay',
            'discount_code'  => 'nullable|string',
        ]);

        $customerId    = $request->user()->id;
        $paymentMethod = $request->payment_method ?? 'cod';

        // Verify address belongs to customer
        $address = CustomerAddress::where('id', $request->address_id)
            ->where('customer_id', $customerId)
            ->first();

        if (!$address) {
            return response()->json(['success' => false, 'message' => 'Invalid address'], 400);
        }

        // Cart check
        $cartItems = CartItem::with(['product', 'color', 'size'])
            ->where('customer_id', $customerId)
            ->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Your cart is empty'], 400);
        }

        // ── Totals ───────────────────────────────────────────────────────────
        $subtotal = $cartItems->sum(fn($item) => $item->price * $item->quantity);

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
                ->valid()
                ->first();

            if (!$discount) {
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

            foreach ($cartItems as $cartItem) {
                OrderItem::create([
                    'order_id'    => $order->id,
                    'product_id'  => $cartItem->product_id,
                    'color_id'    => $cartItem->color_id,
                    'size_id'     => $cartItem->size_id,
                    'quantity'    => $cartItem->quantity,
                    'price'       => $cartItem->price,
                    'subtotal'    => $cartItem->price * $cartItem->quantity,
                    'item_status' => 'pending',
                    'status'      => 'active',
                ]);
            }

            CartItem::where('customer_id', $customerId)->delete();

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
            $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));

            $api->utility->verifyPaymentSignature([
                'razorpay_order_id'   => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature'  => $request->razorpay_signature,
            ]);

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
}
