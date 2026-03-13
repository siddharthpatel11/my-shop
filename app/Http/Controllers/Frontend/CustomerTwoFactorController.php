<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class CustomerTwoFactorController extends Controller
{
    /**
     * Show the 2FA verification form after login
     */
    public function showVerifyForm()
    {
        $customer = Auth::guard('customer')->user();
        
        // If 2FA isn't enabled, redirect to home (shouldn't happen if middleware is correct)
        if (!$customer->google2fa_enabled) {
            return redirect()->route('frontend.home');
        }

        return view('frontend.auth.2fa.verify');
    }

    /**
     * Verify the 2FA OTP code
     */
    public function verify(Request $request)
    {
        $request->validate([
            'one_time_password' => 'required|digits:6',
        ]);

        $customer = Auth::guard('customer')->user();
        $google2fa = app('pragmarx.google2fa');

        if ($google2fa->verifyKey($customer->google2fa_secret, $request->one_time_password, 4)) {
            // Store verification in session specifically for customer guard
            $request->session()->put('customer_2fa_verified', true);
            
            // Redirect to panel or intended route
            return redirect()->intended(route('frontend.my-panel'));
        }

        return back()->withErrors(['one_time_password' => 'Invalid OTP. Please try again.']);
    }

    /**
     * Show the 2FA setup form (QR code generation)
     */
    public function showSetupForm()
    {
        $customer = Auth::guard('customer')->user();
        $google2fa = app('pragmarx.google2fa');

        // Generate secret if not exists
        if (!$customer->google2fa_secret) {
            $customer->google2fa_secret = $google2fa->generateSecretKey();
            $customer->save();
        }

        // Generate QR Code URL
        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config('app.name') . ' (Customer)',
            $customer->email,
            $customer->google2fa_secret
        );

        return view('frontend.auth.2fa.setup', [
            'qrCodeUrl' => $qrCodeUrl,
            'secret' => $customer->google2fa_secret
        ]);
    }

    /**
     * Enable 2FA after verifying the first setup code
     */
    public function enable(Request $request)
    {
        $request->validate([
            'one_time_password' => 'required|digits:6',
        ]);

        $customer = Auth::guard('customer')->user();
        $google2fa = app('pragmarx.google2fa');

        if ($google2fa->verifyKey($customer->google2fa_secret, $request->one_time_password, 4)) {
            $customer->google2fa_enabled = true;
            $customer->save();
            
            // Auto verify session
            $request->session()->put('customer_2fa_verified', true);
            
            return redirect()->route('customer.profile')->with('success', 'Google Authenticator enabled successfully.');
        }

        return back()->withErrors(['one_time_password' => 'Invalid OTP. Please try again.']);
    }

    /**
     * Disable 2FA
     */
    public function disable(Request $request)
    {
        $customer = Auth::guard('customer')->user();
        $customer->update([
            'google2fa_enabled' => false,
            'google2fa_secret' => null
        ]);

        $request->session()->forget('customer_2fa_verified');

        return back()->with('success', 'Google Authenticator has been disabled.');
    }
}
