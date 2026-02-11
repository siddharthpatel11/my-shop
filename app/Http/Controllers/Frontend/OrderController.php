<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        if (!auth('customer')->check()) {
            return redirect()->route('customer.login');
        }

        $orders = Order::with(['items.product', 'items.color', 'items.size', 'address'])
            ->where('customer_id', auth('customer')->id())
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('frontend.orders.index', compact('orders'));
    }

    /**
     * Display order details
     */
    public function show($id)
    {
        if (!auth('customer')->check()) {
            return redirect()->route('customer.login');
        }

        $order = Order::with(['items.product', 'items.color', 'items.size', 'address'])
            ->where('customer_id', auth('customer')->id())
            ->findOrFail($id);

        return view('frontend.orders.show', compact('order'));
    }

    /**
     * Cancel order
     */
    public function cancel($id)
    {
        if (!auth('customer')->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        $order = Order::where('customer_id', auth('customer')->id())
            ->findOrFail($id);

        if (!$order->canBeCancelled()) {
            return response()->json([
                'success' => false,
                'message' => 'Order cannot be cancelled at this stage'
            ], 400);
        }

        $order->update([
            'order_status' => 'cancelled'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Order cancelled successfully'
        ]);
    }
}
