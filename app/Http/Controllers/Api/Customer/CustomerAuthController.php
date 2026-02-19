<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Customer\CustomerResource;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class CustomerAuthController extends Controller
{
    /**
     * Customer Login - returns Sanctum token
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $customer = Customer::where('email', $request->email)->first();

        if (!$customer || !Hash::check($request->password, $customer->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $customer->createToken('customer-api-token')->plainTextToken;

        return response()->json([
            'success'  => true,
            'message'  => 'Login successful',
            'token'    => $token,
            'customer' => new CustomerResource($customer),
        ]);
    }
    /**
     * Customer Logout
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
        ]);
    }
    /**
     * Get authenticated customer profile
     */
    public function profile(Request $request)
    {
        return response()->json([
            'success'  => true,
            'customer' => new CustomerResource($request->user()),
        ]);
    }
}
