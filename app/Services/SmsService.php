<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    /**
     * Send SMS OTP to a phone number.
     *
     * @param string $phone
     * @param string $message
     * @return bool
     */
    public function sendSms($phone, $message)
    {
        // Normalize phone number (E.164 format for Twilio/MSG91)
        // If it's 10 digits, assume India (+91)
        $normalizedPhone = preg_replace('/[^0-9]/', '', $phone);
        if (strlen($normalizedPhone) === 10) {
            $normalizedPhone = '+91' . $normalizedPhone;
        } elseif (!str_starts_with($normalizedPhone, '+')) {
            $normalizedPhone = '+' . $normalizedPhone;
        }

        $apiKey = config('services.sms.api_key');
        $provider = config('services.sms.provider', 'msg91');

        Log::info("Attempting to send SMS via {$provider} to {$normalizedPhone}");

        if (empty($apiKey) && $provider !== 'twilio') {
            Log::warning("SMS not sent: API Key is missing for {$provider}");
            return false;
        }

        try {
            if ($provider === 'msg91') {
                return $this->sendViaMsg91($normalizedPhone, $message, $apiKey);
            } elseif ($provider === 'fast2sms') {
                return $this->sendViaFast2Sms($normalizedPhone, $message, $apiKey);
            } elseif ($provider === 'twilio') {
                return $this->sendViaTwilio($normalizedPhone, $message);
            }

            Log::error("SMS Provider {$provider} is not supported.");
            return false;
        } catch (\Exception $e) {
            Log::error("SMS Sending Failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Twilio Integration using Laravel Http Client
     */
    protected function sendViaTwilio($phone, $message)
    {
        $sid = config('services.twilio.sid');
        $token = config('services.twilio.token');
        $from = config('services.twilio.from');

        if (empty($sid) || empty($token) || empty($from)) {
            Log::warning("Twilio credentials missing in .env");
            return false;
        }

        $response = Http::withBasicAuth($sid, $token)
            ->asForm()
            ->post("https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json", [
                'To'   => $phone,
                'From' => $from,
                'Body' => $message,
            ]);

        if (!$response->successful()) {
            Log::error("Twilio Error: " . $response->body());
        }

        return $response->successful();
    }

    /**
     * Example integration for MSG91
     */
    protected function sendViaMsg91($phone, $message, $apiKey)
    {
        $templateId = config('services.sms.template_id'); // MSG91 often requires a template ID

        $response = Http::withHeaders([
            'authkey' => $apiKey,
            'accept' => 'application/json',
            'content-type' => 'application/json'
        ])->post('https://api.msg91.com/api/v5/otp', [
            'template_id' => $templateId,
            'mobile' => $phone,
            'otp' => $message // In MSG91 /otp endpoint, this is the body or part of it
        ]);

        return $response->successful();
    }

    /**
     * Example integration for Fast2Sms (Very popular in India for simple OTPs)
     */
    protected function sendViaFast2Sms($phone, $message, $apiKey)
    {
        $response = Http::withHeaders([
            'authorization' => $apiKey,
        ])->post('https://www.fast2sms.com/dev/bulkV2', [
            'route' => 'otp',
            'variables_values' => $message,
            'numbers' => $phone,
        ]);

        return $response->successful();
    }
}
