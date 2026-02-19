<?php

namespace App\Http\Resources\Api\Customer;

use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
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
            'cart_items' => CartItemResource::collection($this->resource['cartItems']),
            'summary'    => [
                'subtotal'        => round($this->resource['subtotal'], 2),
                'tax_percent'     => $this->resource['taxPercent'],
                'tax_amount'      => round($this->resource['taxAmount'], 2),
                'discount_code'   => $this->resource['discountCode'],
                'discount_amount' => round($this->resource['discountAmount'], 2),
                'shipping'        => 0,
                'total'           => round($this->resource['total'], 2),
            ],
        ];
    }
}
