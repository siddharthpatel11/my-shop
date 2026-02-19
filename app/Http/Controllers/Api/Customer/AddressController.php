<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Customer\AddressResource;
use App\Models\CustomerAddress;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function index(Request $request)
    {
        $addresses = CustomerAddress::where('customer_id', $request->user()->id)
            ->orderByDesc('is_default')
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'success'   => true,
            'addresses' => AddressResource::collection($addresses),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'country'      => 'required|string|max:255',
            'state'        => 'required|string|max:255',
            'district'     => 'required|string|max:255',
            'city'         => 'required|string|max:255',
            'pincode'      => 'nullable|string|max:10',
            'full_address' => 'nullable|string',
        ]);

        $customerId = $request->user()->id;
        $isFirst    = !CustomerAddress::where('customer_id', $customerId)->exists();

        $address = CustomerAddress::create([
            'customer_id'  => $customerId,
            'country'      => $request->country,
            'state'        => $request->state,
            'district'     => $request->district,
            'city'         => $request->city,
            'pincode'      => $request->pincode,
            'full_address' => $request->full_address,
            'is_default'   => $isFirst,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Address saved successfully',
            'address' => new AddressResource($address),
        ], 201);
    }

    public function show(Request $request, $id)
    {
        $address = CustomerAddress::where('id', $id)
            ->where('customer_id', $request->user()->id)
            ->first();

        if (!$address) {
            return response()->json(['success' => false, 'message' => 'Address not found'], 404);
        }

        return response()->json([
            'success' => true,
            'address' => new AddressResource($address),
        ]);
    }

    public function update(Request $request, $id)
    {
        $address = CustomerAddress::where('id', $id)
            ->where('customer_id', $request->user()->id)
            ->first();

        if (!$address) {
            return response()->json(['success' => false, 'message' => 'Address not found'], 404);
        }

        $request->validate([
            'country'      => 'sometimes|string|max:255',
            'state'        => 'sometimes|string|max:255',
            'district'     => 'sometimes|string|max:255',
            'city'         => 'sometimes|string|max:255',
            'pincode'      => 'nullable|string|max:10',
            'full_address' => 'nullable|string',
        ]);

        $address->update($request->only([
            'country',
            'state',
            'district',
            'city',
            'pincode',
            'full_address',
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Address updated successfully',
            'address' => new AddressResource($address->fresh()),
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $address = CustomerAddress::where('id', $id)
            ->where('customer_id', $request->user()->id)
            ->first();

        if (!$address) {
            return response()->json(['success' => false, 'message' => 'Address not found'], 404);
        }

        $address->delete();

        return response()->json([
            'success' => true,
            'message' => 'Address deleted successfully',
        ]);
    }

    public function setDefault(Request $request, $id)
    {
        $customerId = $request->user()->id;

        $address = CustomerAddress::where('id', $id)
            ->where('customer_id', $customerId)
            ->first();

        if (!$address) {
            return response()->json(['success' => false, 'message' => 'Address not found'], 404);
        }

        CustomerAddress::where('customer_id', $customerId)->update(['is_default' => false]);
        $address->update(['is_default' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Default address updated',
            'address' => new AddressResource($address->fresh()),
        ]);
    }
}
