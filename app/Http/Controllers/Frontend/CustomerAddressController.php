<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\CustomerAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerAddressController extends Controller
{
    /**
     * Store customer address
     */
    public function store(Request $request)
    {
        $request->validate([
            'country' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'pincode' => 'required|string|max:10',
            'full_address' => 'required|string',
        ]);

        $customerId = Auth::guard('customer')->id();

        // If this is the first address or requested to be default
        $isFirstAddress = !CustomerAddress::where('customer_id', $customerId)->exists();
        $isDefault = $isFirstAddress || $request->has('is_default');

        if ($isDefault) {
            CustomerAddress::where('customer_id', $customerId)->update(['is_default' => false]);
        }

        $address = CustomerAddress::create([
            'customer_id' => $customerId,
            'country' => $request->country,
            'state' => $request->state,
            'district' => $request->district,
            'city' => $request->city,
            'pincode' => $request->pincode,
            'full_address' => $request->full_address,
            'is_default' => $isDefault,
            'status' => 'active',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Address added successfully',
            'address' => $address
        ]);
    }

    /**
     * Update customer address
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'country' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'pincode' => 'required|string|max:10',
            'full_address' => 'required|string',
        ]);

        $customerId = Auth::guard('customer')->id();
        $address = CustomerAddress::where('id', $id)->where('customer_id', $customerId)->firstOrFail();

        $address->update([
            'country' => $request->country,
            'state' => $request->state,
            'district' => $request->district,
            'city' => $request->city,
            'pincode' => $request->pincode,
            'full_address' => $request->full_address,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Address updated successfully',
            'address' => $address
        ]);
    }

    /**
     * Delete customer address
     */
    public function destroy($id)
    {
        $customerId = Auth::guard('customer')->id();
        $address = CustomerAddress::where('id', $id)->where('customer_id', $customerId)->firstOrFail();

        if ($address->is_default) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete default address'
            ], 400);
        }

        // Set status to deleted and soft delete
        $address->update(['status' => 'deleted']);
        $address->delete();

        return response()->json([
            'success' => true,
            'message' => 'Address deleted successfully'
        ]);
    }

    /**
     * Set address as default
     */
    public function setDefault($id)
    {
        $customerId = Auth::guard('customer')->id();
        
        CustomerAddress::where('customer_id', $customerId)->update(['is_default' => false]);
        
        $address = CustomerAddress::where('id', $id)->where('customer_id', $customerId)->firstOrFail();
        $address->update(['is_default' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Default address updated'
        ]);
    }
}
