<?php

namespace App\Http\Resources\Api\Customer;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'email'          => $this->email,
            'phone_number'   => $this->phone_number,
            'status'         => $this->status,
            'avatar'         => $this->avatar ? asset('images/customers/' . $this->avatar) : null,
            'cart_count'     => $this->cartItems()->sum('quantity'),
            'order_count'    => $this->orders()->count(),
            'wishlist_count' => $this->wishlist()->count(),
            'addresses'      => AddressResource::collection($this->addresses),
            'created_at'     => $this->created_at,
            'updated_at'     => $this->updated_at,
            'ip_address'     => $this->ip_address,
        ];
    }
}
