<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Customer;
use Closure;

class CustomerAuthController extends Controller
{
    /* ================= REGISTER ================= */

    public function showRegister()
    {
        return view('frontend.auth.register');
    }

    public function register(Request $request)
    {
        try {
            $request->validate([
                'name'         => 'required|string|max:255',
                'email'        => 'required|email|unique:customers,email',
                'phone_number' => 'required|digits_between:10,15|unique:customers,phone_number',
                'password'     => 'required|min:6|confirmed',
            ]);

            Customer::create([
                'name'         => $request->name,
                'email'        => $request->email,
                'phone_number' => $request->phone_number,
                'password'     => Hash::make($request->password),
                'status'       => 'active',
            ]);

            return redirect()
                ->route('customer.login')
                ->with('success', 'Registration successful! Please login to continue.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()
                ->withInput()
                ->with('error', $e->validator->errors()->first());
        }
    }

    /* ================= LOGIN ================= */

    public function showLogin()
    {
        return view('frontend.auth.login');
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email'    => 'required|email',
                'password' => 'required',
            ]);

            $customer = Customer::where('email', $request->email)
                ->where('status', 'active')
                ->first();
            if (!$customer || !Hash::check($request->password, $customer->password)) {
                return back()
                    ->withInput($request->only('email'))
                    ->with('error', 'Invalid credentials or inactive account');
            }

            // Login the customer
            Auth::guard('customer')->login($customer, $request->has('remember'));

            // ✅ Store login data in session
            session()->put([
                'customer_id'    => $customer->id,
                'customer_name'  => $customer->name,
                'customer_email' => $customer->email,
                'login_time'     => now(),
            ]);
            return redirect()->route('frontend.products.index')
                ->with('success', 'Welcome back, ' . $customer->name . '!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()
                ->withInput()
                ->with('error', $e->validator->errors()->first());
        }
    }

    /* ================= PROFILE ================= */

    public function profile()
    {
        return view('frontend.auth.profile', [
            'customer' => Auth::guard('customer')->user()
        ]);
    }

    /* ================= LOGOUT ================= */

    public function logout()
    {
        Auth::guard('customer')->logout();

        return redirect()
            ->route('customer.login')
            ->with('success', 'Logged out successfully');
    }
    public function handle($request, Closure $next)
    {
        if (!auth('customer')->check()) {
            // For AJAX requests, return JSON
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated',
                    'redirect' => route('customer.login')
                ], 401);
            }

            // For regular requests, redirect to login
            return redirect()->route('customer.login');
        }

        return $next($request);
    }
}
