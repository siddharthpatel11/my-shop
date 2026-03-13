<?php

use App\Models\Customer;
use PragmaRX\Google2FA\Google2FA;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$secret = 'SH5L4GKZ33LAQ5WF';
$otpAttempt = '105944';

$google2fa = app('pragmarx.google2fa');
$currentOtp = $google2fa->getCurrentOtp($secret);
$timestamp = $google2fa->getTimestamp();
$serverTime = date('Y-m-d H:i:s');

echo "Server Time: $serverTime\n";
echo "Secret: $secret\n";
echo "Current OTP: $currentOtp\n";
echo "Attempted OTP: $otpAttempt\n";

$isValid = $google2fa->verifyKey($secret, $otpAttempt, 10); // Window of 10
echo "Is Valid (window 10): " . ($isValid ? "YES" : "NO") . "\n";

// Check if secret is exactly 16 chars and valid base32
echo "Secret Length: " . strlen($secret) . "\n";
