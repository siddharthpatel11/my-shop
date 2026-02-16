<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    public function storeToken(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        $user = auth()->user();

        if ($user) {
            $user->update(['fcm_token' => $request->token]);
            Log::info('FCM Token updated for user: ' . $user->id);
            return response()->json(['success' => true, 'message' => 'Token saved successfully']);
        }

        return response()->json(['success' => false, 'message' => 'User not authenticated'], 401);
    }
}
