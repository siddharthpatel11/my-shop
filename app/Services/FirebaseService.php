<?php

namespace App\Services;

use Google\Client;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FirebaseService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setAuthConfig(storage_path('app/firebase-auth.json'));
        $this->client->addScope('https://www.googleapis.com/auth/firebase.messaging');
    }

    public function getAccessToken()
    {
        $this->client->fetchAccessTokenWithAssertion();
        $token = $this->client->getAccessToken();
        return $token['access_token'];
    }

    public function sendNotification($title, $body, $fcmToken, $data = [])
    {
        try {
            $accessToken = $this->getAccessToken();
            $projectId = config('services.firebase.project_id', 'my-shop-a2caf');

            $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

            $payload = [
                'message' => [
                    'token' => $fcmToken,
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                    ],
                    'data' => $data,
                ],
            ];

            $response = Http::withToken($accessToken)
                ->contentType('application/json')
                ->post($url, $payload);

            if ($response->successful()) {
                Log::info('FCM Notification sent successfully to ' . $fcmToken);
                return true;
            } else {
                Log::error('FCM Notification Error: ' . $response->body());
                return false;
            }
        } catch (\Exception $e) {
            Log::error('Firebase Service Error: ' . $e->getMessage());
            return false;
        }
    }
}
