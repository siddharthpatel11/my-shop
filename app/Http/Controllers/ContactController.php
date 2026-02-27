<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    /**
     * Handle contact form submission
     */
    public function submit(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'number' => 'required|string|max:20',
            'message' => 'required|string|max:2000',
        ]);

        try {
            // Save to database
            $contact = Contact::create($validated);

            // Send Email to Admin(s)
            try {
                $admins = \App\Models\User::all();

                if ($admins->count() > 0) {
                    foreach ($admins as $admin) {
                        Mail::to($admin->email)->send(new \App\Mail\ContactMessageNotification($contact));
                    }
                } else {
                    // Fallback to .env email if no admins found in database
                    $adminEmail = env('MAIL_ADMIN_EMAIL', 'siddharthchhayani11@gmail.com');
                    Mail::to($adminEmail)->send(new \App\Mail\ContactMessageNotification($contact));
                }
            } catch (\Exception $e) {
                Log::error('Failed to send contact email: ' . $e->getMessage());
            }

            // For now, just log it
            Log::info('Contact Form Submission:', $validated);

            if ($request->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Thank you for contacting us! We will get back to you soon.'
                ]);
            }

            return redirect()->back()->with('contact_success', 'Thank you for contacting us! We will get back to you soon.');
        } catch (\Exception $e) {
            Log::error('Contact Form Error: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Sorry, something went wrong. Please try again later.'
                ], 500);
            }

            return redirect()->back()->with('contact_error', 'Sorry, something went wrong. Please try again later.');
        }
    }
}
