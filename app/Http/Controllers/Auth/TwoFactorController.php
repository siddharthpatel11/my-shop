<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PragmaRX\Google2FALaravel\Support\Authenticator;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TwoFactorController extends Controller
{
    public function showVerifyForm()
    {
        $user = Auth::user();
        $google2fa = app('pragmarx.google2fa');

        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $user->google2fa_secret
        );

        return view('auth.2fa.verify', [
            'qrCodeUrl' => $qrCodeUrl,
            'secret' => $user->google2fa_secret
        ]);
    }

    public function verify(Request $request)
    {
        $request->validate([
            'one_time_password' => 'required|digits:6',
        ]);

        $user = Auth::user();
        $google2fa = app('pragmarx.google2fa');

        if ($google2fa->verifyKey($user->google2fa_secret, $request->one_time_password)) {
            $request->session()->put('2fa_verified', true);
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors(['one_time_password' => 'Invalid OTP. Please try again.']);
    }

    public function showSetupForm()
    {
        $user = Auth::user();
        $google2fa = app('pragmarx.google2fa');

        if (!$user->google2fa_secret) {
            $user->google2fa_secret = $google2fa->generateSecretKey();
            $user->save();
        }

        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $user->google2fa_secret
        );

        return view('auth.2fa.setup', [
            'qrCodeUrl' => $qrCodeUrl,
            'secret' => $user->google2fa_secret
        ]);
    }

    public function enable(Request $request)
    {
        $request->validate([
            'one_time_password' => 'required|digits:6',
        ]);

        $user = Auth::user();
        $google2fa = app('pragmarx.google2fa');

        if ($google2fa->verifyKey($user->google2fa_secret, $request->one_time_password)) {
            $user->google2fa_enabled = true;
            $user->save();
            $request->session()->put('2fa_verified', true);
            return redirect()->route('dashboard')->with('success', 'Google Authenticator enabled successfully.');
        }

        return back()->withErrors(['one_time_password' => 'Invalid OTP. Please try again.']);
    }
}
