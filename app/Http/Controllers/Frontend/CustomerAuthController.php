<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Customer;
use Closure;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailChangeOTPMail;
use Carbon\Carbon;
use App\Services\SmsService;

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
            ], [
                'phone_number.unique' => 'This phone number is already registered with another account.',
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
        $customer = Auth::guard('customer')->user();

        // Fetch counts for the dashboard
        $cartCount = \App\Models\CartItem::where('customer_id', $customer->id)->sum('quantity');
        $orderCount = \App\Models\Order::where('customer_id', $customer->id)->count();
        $wishlistCount = \App\Models\Wishlist::where('customer_id', $customer->id)->count();

        return view('frontend.auth.profile', compact('customer', 'cartCount', 'orderCount', 'wishlistCount'));
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

    /* ================= EMAIL CHANGE ================= */
    public function sendEmailChangeOTP(Request $request)
    {
        $customer = Auth::guard('customer')->user();

        // Generate 6-digit OTP
        $otp = rand(100000, 999999);

        // Save OTP to customer record
        $customer->update([
            'email_otp' => $otp,
            'email_otp_expires_at' => Carbon::now()->addMinutes(10),
        ]);

        // Send OTP via email
        try {
            Mail::to($customer->email)->send(new EmailChangeOTPMail($otp));
            return response()->json(['success' => true, 'message' => 'OTP sent successfully to your current email.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to send OTP. Please try again. ' . $e->getMessage()], 500);
        }
    }

    public function verifyEmailChangeOTP(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        $customer = Auth::guard('customer')->user();

        if ($customer->email_otp === $request->otp && Carbon::now()->isBefore($customer->email_otp_expires_at)) {
            // OTP is correct and not expired
            // We can mark the OTP as verified in session to allow the next step
            session()->put('email_otp_verified', true);
            return response()->json(['success' => true, 'message' => 'OTP verified successfully.']);
        }

        return response()->json(['success' => false, 'message' => 'Invalid or expired OTP.'], 422);
    }

    public function updateEmail(Request $request)
    {
        $request->validate([
            'new_email' => 'required|email',
        ]);

        // Check if email already exists
        $existing = Customer::where('email', $request->new_email)->exists();
        if ($existing) {
            return response()->json(['success' => false, 'message' => 'This email address is already registered with another account.'], 200);
        }

        if (!session()->get('email_otp_verified')) {
            return response()->json(['success' => false, 'message' => 'Please verify your current email first.'], 403);
        }

        $customer = Auth::guard('customer')->user();

        $customer->update([
            'email' => $request->new_email,
            'email_otp' => null,
            'email_otp_expires_at' => null,
        ]);

        session()->forget('email_otp_verified');

        return response()->json(['success' => true, 'message' => 'Email updated successfully.']);
    }

    /* ================= PHONE CHANGE ================= */
    public function sendPhoneChangeOTP(Request $request)
    {
        $customer = Auth::guard('customer')->user();

        // Generate 6-digit OTP
        $otp = rand(100000, 999999);

        // Save OTP to customer record
        $customer->update([
            'phone_otp' => $otp,
            'phone_otp_expires_at' => Carbon::now()->addMinutes(10),
        ]);

        // Send OTP via SMS
        try {
            $smsService = new SmsService();
            $messageTemplate = config('services.twilio.otp_message', 'Your verification code for phone change is: [OTP]');
            $message = str_replace('[OTP]', $otp, $messageTemplate);
            $smsSent = $smsService->sendSms($customer->phone_number, $message);

            if ($smsSent) {
                return response()->json([
                    'success' => true,
                    'message' => 'OTP sent successfully to your current phone number.'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send OTP via SMS. Please check your SMS provider configuration.'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to send OTP. Please try again.'], 500);
        }
    }

    public function verifyPhoneChangeOTP(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        $customer = Auth::guard('customer')->user();

        if ($customer->phone_otp === $request->otp && Carbon::now()->isBefore($customer->phone_otp_expires_at)) {
            session()->put('phone_otp_verified', true);
            return response()->json(['success' => true, 'message' => 'OTP verified successfully.']);
        }

        return response()->json(['success' => false, 'message' => 'Invalid or expired OTP.'], 422);
    }

    public function updatePhone(Request $request)
    {
        $request->validate([
            'new_phone' => 'required|digits_between:10,15',
        ]);

        // Check if phone already exists
        $existing = Customer::where('phone_number', $request->new_phone)->exists();
        if ($existing) {
            return response()->json(['success' => false, 'message' => 'This phone number is already registered with another account.'], 200);
        }

        if (!session()->get('phone_otp_verified')) {
            return response()->json(['success' => false, 'message' => 'Please verify your current phone number first.'], 403);
        }

        $customer = Auth::guard('customer')->user();

        $customer->update([
            'phone_number' => $request->new_phone,
            'phone_otp' => null,
            'phone_otp_expires_at' => null,
        ]);

        session()->forget('phone_otp_verified');

        return response()->json(['success' => true, 'message' => 'Phone number updated successfully.']);
    }
}
