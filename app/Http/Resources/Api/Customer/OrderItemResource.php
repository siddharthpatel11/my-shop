<?php

namespace App\Http\Resources\Api\Customer;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        $images     = $this->product->image ? explode(',', $this->product->image) : [];
        $firstImage = !empty($images) ? $images[0] : null;

        return [
            'id'          => $this->id,
            'product'     => [
                'id'       => $this->product->id,
                'name'     => $this->product->name,
                'category' => $this->product->category->name ?? null,
                'image'    => $firstImage
                    ? asset('images/products/' . $firstImage)
                    : null,
            ],
            'color'       => $this->when($this->color, fn() => [
                'id'       => $this->color->id,
                'name'     => $this->color->name,
                'hex_code' => $this->color->hex_code,
            ]),
            'size'        => $this->when($this->size, fn() => [
                'id'   => $this->size->id,
                'name' => $this->size->name,
                'code' => $this->size->code ?? null,
            ]),
            'quantity'    => $this->quantity,
            'price'       => (float) $this->price,
            'subtotal'    => (float) $this->subtotal,
            'item_status' => $this->item_status,
            'status_label' => $this->item_status_label,
            'status_badge_color' => $this->item_status_badge_color,
        ];
    }
}
