<?php

namespace App\Http\Resources\Api\Customer;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'id'                  => $this->id,
            'order_number'        => $this->order_number,
            'order_status'        => $this->order_status,
            'order_status_badge'  => $this->order_status_badge_color,
            'payment_status'      => $this->payment_status,
            'payment_status_badge' => $this->payment_status_badge_color,
            'payment_method'      => $this->payment_method,
            'payment_method_name' => $this->payment_method_name,
            'subtotal'            => (float) $this->subtotal,
            'tax_amount'          => (float) $this->tax_amount,
            'discount'            => (float) $this->discount,
            'discount_code'       => $this->discount_code,
            'shipping'            => (float) $this->shipping,
            'total'               => (float) $this->total,
            'items_count'         => $this->items->count(),
            'can_cancel'          => $this->canBeCancelled(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
