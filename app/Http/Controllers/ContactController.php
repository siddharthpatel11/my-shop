<?php

namespace App\Http\Controllers;

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
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
            'page_id' => 'nullable|exists:pages,id',
        ]);

        try {
            // You can send email here
            // Mail::to('your-email@example.com')->send(new ContactFormMail($validated));

            // Or save to database
            // ContactSubmission::create($validated);

            // For now, just log it
            Log::info('Contact Form Submission:', $validated);

            return redirect()->back()->with('contact_success', 'Thank you for contacting us! We will get back to you soon.');
        } catch (\Exception $e) {
            Log::error('Contact Form Error: ' . $e->getMessage());
            return redirect()->back()->with('contact_error', 'Sorry, something went wrong. Please try again later.');
        }
    }
}
