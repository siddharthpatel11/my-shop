<?php

namespace App\Http\Resources\Api\Customer;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
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
            'id'           => $this->id,
            'country'      => $this->country,
            'state'        => $this->state,
            'district'     => $this->district,
            'city'         => $this->city,
            'pincode'      => $this->pincode,
            'full_address' => $this->full_address,
            'is_default'   => (bool) $this->is_default,
            'created_at'   => $this->created_at?->toDateTimeString(),
        ];
    }
}
