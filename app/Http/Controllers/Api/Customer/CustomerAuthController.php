<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Customer\CustomerResource;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailChangeOTPMail;
use Carbon\Carbon;
use App\Services\SmsService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;

class CustomerAuthController extends Controller
{
    /**
     * Customer Registration
     */
    public function register(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|unique:customers,email',
            'phone_number' => 'required|digits_between:10,15|unique:customers,phone_number',
            'password'     => 'required|min:6|confirmed',
        ]);

        $customer = Customer::create([
            'name'         => $request->name,
            'email'        => $request->email,
            'phone_number' => $request->phone_number,
            'password'     => Hash::make($request->password),
            'status'       => 'active',
        ]);

        $token = $customer->createToken('customer-api-token')->plainTextToken;

        return response()->json([
            'success'  => true,
            'message'  => 'Registration successful',
            'token'    => $token,
            'customer' => new CustomerResource($customer),
        ], 201);
    }

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

        // If 2FA is enabled, don't return the token yet, or mark it as unverified
        if ($customer->google2fa_enabled) {
            return response()->json([
                'success'  => true,
                'message'  => '2FA verification required',
                '2fa_required' => true,
                'customer' => new CustomerResource($customer),
            ]);
        }

        return response()->json([
            'success'  => true,
            'message'  => 'Login successful',
            'token'    => $token,
            'customer' => new CustomerResource($customer),
        ]);
    }

    /**
     * Verify 2FA OTP for API login
     */
    public function verify2FA(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'one_time_password' => 'required|digits:6',
        ]);

        $customer = Customer::where('email', $request->email)->first();

        if (!$customer || !Hash::check($request->password, $customer->password)) {
            return response()->json(['success' => false, 'message' => 'Invalid credentials'], 401);
        }

        $google2fa = app('pragmarx.google2fa');

        Log::info('2FA Verify Attempt (Login)', [
            'customer_id' => $customer->id,
            'secret' => $customer->google2fa_secret,
            'otp' => $request->one_time_password,
            'window' => 4
        ]);

        // Increase window to 4 (allows 2 minutes drift) for better user experience
        if ($google2fa->verifyKey($customer->google2fa_secret, $request->one_time_password, 4)) {
            $token = $customer->createToken('customer-api-token')->plainTextToken;
            return response()->json([
                'success'  => true,
                'message'  => '2FA verification successful',
                'token'    => $token,
                'customer' => new CustomerResource($customer),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid 2FA code',
        ], 422);
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

    /**
     * Get 2FA Setup Data (QR Code URL and Secret)
     */
    public function setup2FA(Request $request)
    {
        $customer = $request->user();
        $google2fa = app('pragmarx.google2fa');

        if (!$customer->google2fa_secret) {
            $customer->google2fa_secret = $google2fa->generateSecretKey();
            $customer->save();
        }

        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config('app.name') . ' (Customer API)',
            $customer->email,
            $customer->google2fa_secret
        );

        return response()->json([
            'success' => true,
            'qr_code_url' => $qrCodeUrl,
            'secret' => $customer->google2fa_secret,
            'is_enabled' => (bool)$customer->google2fa_enabled
        ]);
    }

    /**
     * Enable 2FA for the authenticated customer
     */
    public function enable2FA(Request $request)
    {
        $request->validate([
            'one_time_password' => 'required|digits:6',
        ]);

        $customer = $request->user();
        $google2fa = app('pragmarx.google2fa');

        Log::info('2FA Enable Attempt', [
            'customer_id' => $customer->id,
            'secret' => $customer->google2fa_secret,
            'otp' => $request->one_time_password,
            'window' => 4
        ]);

        // Increase window to 4
        if ($google2fa->verifyKey($customer->google2fa_secret, $request->one_time_password, 4)) {
            $customer->google2fa_enabled = true;
            $customer->save();

            return response()->json([
                'success' => true,
                'message' => 'Google Authenticator enabled successfully.',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid OTP code.',
        ], 422);
    }

    /**
     * Disable 2FA for the authenticated customer
     */
    public function disable2FA(Request $request)
    {
        $customer = $request->user();
        $customer->update([
            'google2fa_enabled' => false,
            'google2fa_secret' => null
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Google Authenticator disabled successfully.',
        ]);
    }

    /**
     * Update basic profile info (Name and Avatar)
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'name'   => 'required|string|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $customer = $request->user();

        try {
            $customer->name = $request->name;

            if ($request->hasFile('avatar')) {
                // Delete old avatar if exists
                if ($customer->avatar && file_exists(public_path('images/customers/' . $customer->avatar))) {
                    unlink(public_path('images/customers/' . $customer->avatar));
                }

                $image = $request->file('avatar');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('images/customers/'), $imageName);
                $customer->avatar = $imageName;
            }

            $customer->save();

            return response()->json([
                'success'  => true,
                'message'  => 'Profile updated successfully',
                'customer' => new CustomerResource($customer),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove profile image
     */
    public function removeAvatar(Request $request)
    {
        $customer = $request->user();

        try {
            if ($customer->avatar) {
                // Delete image file if exists
                if (file_exists(public_path('images/customers/' . $customer->avatar))) {
                    unlink(public_path('images/customers/' . $customer->avatar));
                }

                $customer->avatar = null;
                $customer->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Profile image removed successfully!',
                    'customer' => new CustomerResource($customer),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'No profile image to remove.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /* ================= EMAIL CHANGE ================= */
    public function sendEmailChangeOTP(Request $request)
    {
        $customer = $request->user();

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
            return response()->json(['success' => false, 'message' => 'Failed to send OTP. Please try again.'], 500);
        }
    }

    public function verifyEmailChangeOTP(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        $customer = $request->user();

        if ($customer->email_otp === $request->otp && Carbon::now()->isBefore($customer->email_otp_expires_at)) {
            // Store verification in Cache (stateless)
            Cache::put('email_otp_verified_' . $customer->id, true, now()->addMinutes(10));
            return response()->json(['success' => true, 'message' => 'OTP verified successfully.']);
        }

        return response()->json(['success' => false, 'message' => 'Invalid or expired OTP.'], 422);
    }

    public function updateEmail(Request $request)
    {
        $request->validate([
            'new_email' => 'required|email',
        ]);

        $customer = $request->user();

        // Check verification in Cache
        if (!Cache::get('email_otp_verified_' . $customer->id)) {
            return response()->json(['success' => false, 'message' => 'Please verify your current email first.'], 403);
        }

        // Check if email already exists
        $existing = Customer::where('email', $request->new_email)->where('id', '!=', $customer->id)->exists();
        if ($existing) {
            return response()->json(['success' => false, 'message' => 'This email address is already registered with another account.'], 422);
        }

        $customer->update([
            'email' => $request->new_email,
            'email_otp' => null,
            'email_otp_expires_at' => null,
        ]);

        Cache::forget('email_otp_verified_' . $customer->id);

        return response()->json(['success' => true, 'message' => 'Email updated successfully.']);
    }

    /* ================= PHONE CHANGE ================= */
    public function sendPhoneChangeOTP(Request $request)
    {
        $customer = $request->user();

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
                return response()->json(['success' => true, 'message' => 'OTP sent successfully to your current phone number.']);
            } else {
                return response()->json(['success' => false, 'message' => 'Failed to send OTP via SMS. Please check your SMS provider configuration.']);
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

        $customer = $request->user();

        if ($customer->phone_otp === $request->otp && Carbon::now()->isBefore($customer->phone_otp_expires_at)) {
            // Store verification in Cache (stateless)
            Cache::put('phone_otp_verified_' . $customer->id, true, now()->addMinutes(10));
            return response()->json(['success' => true, 'message' => 'OTP verified successfully.']);
        }

        return response()->json(['success' => false, 'message' => 'Invalid or expired OTP.'], 422);
    }

    public function updatePhone(Request $request)
    {
        $request->validate([
            'new_phone' => 'required|digits_between:10,15',
        ]);

        $customer = $request->user();

        // Check verification in Cache
        if (!Cache::get('phone_otp_verified_' . $customer->id)) {
            return response()->json(['success' => false, 'message' => 'Please verify your current phone number first.'], 403);
        }

        // Check if phone already exists
        $existing = Customer::where('phone_number', $request->new_phone)->where('id', '!=', $customer->id)->exists();
        if ($existing) {
            return response()->json(['success' => false, 'message' => 'This phone number is already registered with another account.'], 422);
        }

        $customer->update([
            'phone_number' => $request->new_phone,
            'phone_otp' => null,
            'phone_otp_expires_at' => null,
        ]);

        Cache::forget('phone_otp_verified_' . $customer->id);

        return response()->json(['success' => true, 'message' => 'Phone number updated successfully.']);
    }

    /**
     * Forgot Password API - Sends a reset link email
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $customer = Customer::where('email', $request->email)->first();

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Customer not found with this email'
            ], 404);
        }

        // Send reset link using the 'customers' broker
        $status = Password::broker('customers')->sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? response()->json([
                'success' => true,
                'message' => Lang::get($status)
            ])
            : response()->json([
                'success' => false,
                'message' => Lang::get($status)
            ], 500);
    }

    /**
     * Reset Password API - Resets password using a token
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email'                 => 'required|email',
            'token'                 => 'required',
            'password'              => 'required|min:6|confirmed',
        ]);

        $customer = Customer::where('email', $request->email)->first();

        // Check if new password is same as old password
        if ($customer && Hash::check($request->password, $customer->password)) {
            return response()->json([
                'success' => false,
                'message' => 'New password cannot be same as old password'
            ], 422);
        }

        $status = Password::broker('customers')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($customer, $password) {
                $customer->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $customer->save();

                event(new PasswordReset($customer));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? response()->json([
                'success' => true,
                'message' => Lang::get($status)
            ])
            : response()->json([
                'success' => false,
                'message' => Lang::get($status)
            ], 400);
    }

}
