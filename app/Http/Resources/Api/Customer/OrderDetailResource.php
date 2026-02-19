<?php

namespace App\Http\Resources\Api\Customer;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        return [
            'id'                   => $this->id,
            'order_number'         => $this->order_number,
            'order_status'         => $this->order_status,
            'order_status_badge'   => $this->order_status_badge_color,
            'payment_status'       => $this->payment_status,
            'payment_status_badge' => $this->payment_status_badge_color,
            'payment_method'       => $this->payment_method,
            'payment_method_name'  => $this->payment_method_name,
            'payment_method_icon'  => $this->payment_method_icon,

            // Pricing breakdown
            'subtotal'             => (float) $this->subtotal,
            'tax_percent'          => $this->tax_percentage,
            'tax_amount'           => (float) $this->tax_amount,
            'discount'             => (float) $this->discount,
            'discount_code'        => $this->discount_code,
            'shipping'             => (float) $this->shipping,
            'total'                => (float) $this->total,

            // Razorpay IDs (only if payment was online)
            'razorpay_order_id'    => $this->when(
                $this->payment_method === 'razorpay',
                $this->razorpay_order_id
            ),
            'razorpay_payment_id'  => $this->when(
                $this->payment_method === 'razorpay',
                $this->razorpay_payment_id
            ),

            // Delivery info
            'delivery_status'      => $this->getDeliveryStatusMessage(),
            'has_partial_delivery' => $this->hasPartialDelivery(),
            'can_cancel'           => $this->canBeCancelled(),

            // Address
            'address'              => $this->when($this->address, fn() => new AddressResource($this->address)),

            // Items
            'items'                => OrderItemResource::collection($this->items),

            'notes'                => $this->notes,
            'created_at'           => $this->created_at?->toDateTimeString(),
            'created_at_human'     => $this->created_at?->format('d M, Y \a\t h:i A'),
            'updated_at'           => $this->updated_at?->toDateTimeString(),
        ];
    }
}
