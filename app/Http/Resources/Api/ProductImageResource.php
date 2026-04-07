<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductImageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'    => $this->id,
            'url'   => url('images/products/' . $this->image),
            'variant' => $this->variant_name,
            'price' => $this->price,
            'stock' => $this->stock,
            'color' => $this->whenLoaded('color', function () {
                return [
                    'id'       => $this->color->id,
                    'name'     => $this->color->name,
                    'hex_code' => $this->color->hex_code,
                ];
            }, [
                'id' => $this->color_id,
            ]),
            'size' => $this->whenLoaded('size', function () {
                return [
                    'id'   => $this->size->id,
                    'name' => $this->size->name,
                    'code' => $this->size->code,
                ];
            }, [
                'id' => $this->size_id,
            ]),
            'sort_order' => $this->sort_order,
        ];
    }
}
