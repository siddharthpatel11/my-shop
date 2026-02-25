<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\OrderStatusChanged;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Mail\PartialDeliveryNotification;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AdminOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['customer', 'items.product', 'address', 'tax'])->where('status', 'active');

        // Filter by status
        if ($request->filled('order_status')) {
            $query->where('order_status', $request->order_status);
        }

        // Filter by payment status
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Search by order number or customer name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'LIKE', "%{$search}%")
                    ->orWhereHas('customer', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%{$search}%")
                            ->orWhere('email', 'LIKE', "%{$search}%");
                    });
            });
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->orderBy('created_at', 'asc')->paginate(10);

        // Statistics
        $stats = [
            'total_orders' => Order::where('status', 'active')->count(),
            'pending_orders' => Order::where('status', 'active')->where('order_status', 'pending')->count(),
            'total_revenue' => Order::where('status', 'active')->where('payment_status', 'paid')->sum('total'),
            'pending_payments' => Order::where('status', 'active')->where('payment_status', 'pending')->sum('total'),
        ];

        return view('admin.orders.index', compact('orders', 'stats'));
    }

    /**
     * Display order details
     */
    public function show($id)
    {
        $order = Order::with([
            'customer',
            'address',
            'items.product.category',
            'items.color',
            'items.size',
            'tax'
        ])->where('status', 'active')->findOrFail($id);

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Update order status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'order_status' => 'required|in:pending,processing,shipped,delivered,cancelled'
        ]);

        DB::beginTransaction();
        try {
            $order = Order::with(['customer', 'items'])->findOrFail($id);
            $oldStatus = $order->order_status;
            $newStatus = $request->order_status;

            // Update order status
            $order->update(['order_status' => $newStatus]);

            // Update all order items status based on order status
            $itemStatus = match ($newStatus) {
                'pending' => 'pending',
                'processing' => 'available',
                'shipped' => 'available',
                'delivered' => 'delivered',
                'cancelled' => 'cancelled',
                default => 'pending',
            };

            // Update all items in this order
            OrderItem::where('order_id', $id)->update(['item_status' => $itemStatus]);

            // Reset partial delivery notified flag since all items are being updated together
            $order->update(['partial_delivery_notified' => false]);

            DB::commit();

            // Load relationships for email
            $order->load([
                'customer',
                'address',
                'items.product',
                'items.color',
                'items.size',
                'tax'
            ]);

            // Send email notification to customer
            try {
                Mail::to($order->customer->email)->send(new OrderStatusChanged($order, $oldStatus, $newStatus));
                Log::info('Order status email sent successfully to: ' . $order->customer->email);

                return redirect()->back()->with('success', 'Order status updated successfully. Customer has been notified via email.');
            } catch (\Exception $e) {
                Log::error('Failed to send order status email: ' . $e->getMessage());
                Log::error($e->getTraceAsString());

                return redirect()->back()->with('warning', 'Order status updated successfully, but failed to send email notification.');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update order status: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Failed to update order status: ' . $e->getMessage());
        }
    }

    /**
     * Update payment status
     */
    public function updatePaymentStatus(Request $request, $id)
    {
        $request->validate([
            'payment_status' => 'required|in:pending,paid,failed'
        ]);

        $order = Order::findOrFail($id);
        $order->update(['payment_status' => $request->payment_status]);

        return redirect()->back()->with('success', 'Payment status updated successfully');
    }

    /**
     * Process partial delivery
     */
    public function processPartialDelivery(Request $request, $id)
    {
        // Validate request
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:customer_order_items,id',
            'items.*.item_status' => 'required|in:pending,available,out_of_stock,delivered,cancelled',
            'items.*.notes' => 'nullable|string|max:500'
        ]);

        Log::info('=== PARTIAL DELIVERY PROCESS STARTED ===');
        Log::info('Order ID: ' . $id);
        Log::info('Request data: ' . json_encode($request->items));

        DB::beginTransaction();

        try {
            $order = Order::findOrFail($id);

            Log::info('Order found: #' . $order->order_number);

            // Update each item
            foreach ($request->items as $itemData) {
                $item = OrderItem::where('order_id', $id)
                    ->where('id', $itemData['id'])
                    ->first();

                if ($item) {
                    Log::info('Updating item ID: ' . $item->id . ' to status: ' . $itemData['item_status']);

                    $item->update([
                        'item_status' => $itemData['item_status'],
                        'notes' => $itemData['notes'] ?? null,
                    ]);

                    Log::info('Item updated successfully. New status: ' . $item->fresh()->item_status);
                } else {
                    Log::warning('Item not found: ' . $itemData['id']);
                }
            }

            // Always reset the flag so email can fire again on each save
            Order::where('id', $id)->update(['partial_delivery_notified' => false]);

            DB::commit();
            Log::info('Database transaction committed');

            // Reload the order completely fresh from DB with all relationships
            $order = Order::with([
                'customer',
                'address',
                'activeItems.product',
                'activeItems.color',
                'activeItems.size',
                'tax'
            ])->find($id);

            // Check counts for debugging
            $totalItems = $order->activeItems()->count();
            $availableItems = $order->availableItems()->count();
            $deliveredItems = $order->deliveredItems()->count();
            $outOfStockItems = $order->outOfStockItems()->count();

            Log::info('Item counts:');
            Log::info('- Total active: ' . $totalItems);
            Log::info('- Available: ' . $availableItems);
            Log::info('- Delivered: ' . $deliveredItems);
            Log::info('- Out of stock: ' . $outOfStockItems);

            // Use hasOutOfStockItems: sends email whenever ANY item is out_of_stock,
            // even if it is the only item in the order.
            $shouldNotify = $order->hasOutOfStockItems();
            Log::info('Should notify (hasOutOfStockItems): ' . ($shouldNotify ? 'YES' : 'NO'));

            if ($shouldNotify) {
                try {
                    Log::info('Attempting to send out-of-stock notification email to: ' . $order->customer->email);

                    Mail::to($order->customer->email)
                        ->send(new PartialDeliveryNotification($order));

                    $order->update(['partial_delivery_notified' => true]);
                    Log::info('Out-of-stock notification email sent successfully!');

                    return redirect()->back()->with('success', 'Item statuses updated successfully. Customer has been notified via email.');
                } catch (\Exception $e) {
                    Log::error('Failed to send notification email: ' . $e->getMessage());
                    Log::error('Email error trace: ' . $e->getTraceAsString());

                    return redirect()->back()->with('warning', 'Item statuses updated, but failed to send email notification: ' . $e->getMessage());
                }
            } else {
                Log::info('No out-of-stock items — email not sent');
                return redirect()->back()->with('success', 'Item statuses updated successfully.');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update item statuses: ' . $e->getMessage());
            Log::error('Update error trace: ' . $e->getTraceAsString());

            return redirect()->back()->with('error', 'Failed to update item statuses: ' . $e->getMessage());
        }
    }

    /**
     * Add notes to order
     */
    public function addNotes(Request $request, $id)
    {
        $request->validate([
            'notes' => 'required|string|max:1000'
        ]);

        $order = Order::findOrFail($id);
        $order->update(['notes' => $request->notes]);

        return redirect()->back()->with('success', 'Notes added successfully');
    }

    /**
     * Delete order
     */
    public function destroy($id)
    {
        $order = Order::findOrFail($id);

        if (!in_array($order->order_status, ['cancelled'])) {
            return redirect()->back()->with('error', 'Only cancelled orders can be deleted');
        }

        $order->update(['status' => 'deleted']);

        return redirect()->route('admin.orders.index')->with('success', 'Order deleted successfully');
    }

    /**
     * Export orders to CSV
     */
    // public function export(Request $request)
    // {
    //     $query = Order::with(['customer', 'items.product'])->where('status', 'active');

    //     // Apply same filters as index
    //     if ($request->filled('order_status')) {
    //         $query->where('order_status', $request->order_status);
    //     }
    //     if ($request->filled('payment_status')) {
    //         $query->where('payment_status', $request->payment_status);
    //     }

    //     $orders = $query->latest()->get();

    //     $filename = 'orders_' . date('Y-m-d_His') . '.csv';

    //     $headers = [
    //         'Content-Type' => 'text/csv',
    //         'Content-Disposition' => "attachment; filename=\"{$filename}\"",
    //     ];

    //     $callback = function () use ($orders) {
    //         $file = fopen('php://output', 'w');

    //         // Header row
    //         fputcsv($file, [
    //             'Order Number',
    //             'Customer Name',
    //             'Customer Email',
    //             'Order Date',
    //             'Order Status',
    //             'Payment Status',
    //             'Subtotal',
    //             'Tax',
    //             'Discount',
    //             'Total',
    //             'Items Count',
    //             'Delivery Status'
    //         ]);

    //         // Data rows
    //         foreach ($orders as $order) {
    //             fputcsv($file, [
    //                 $order->order_number,
    //                 $order->customer->name,
    //                 $order->customer->email,
    //                 $order->created_at->format('Y-m-d H:i:s'),
    //                 $order->order_status,
    //                 $order->payment_status,
    //                 $order->subtotal,
    //                 $order->tax_amount,
    //                 $order->discount,
    //                 $order->total,
    //                 $order->items->count(),
    //                 $order->getDeliveryStatusMessage()
    //             ]);
    //         }

    //         fclose($file);
    //     };

    //     return response()->stream($callback, 200, $headers);
    // }
}
