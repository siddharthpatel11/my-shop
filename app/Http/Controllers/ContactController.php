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
            Contact::create($validated);

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
