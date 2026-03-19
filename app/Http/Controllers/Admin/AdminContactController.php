<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;

class AdminContactController extends Controller
{
    /**
     * Display a listing of contact messages.
     */
    public function index(Request $request)
    {
        $query = Contact::query();

        // Search Filter (Single Input for Name, Email, Phone)
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('email', 'like', '%' . $searchTerm . '%')
                  ->orWhere('number', 'like', '%' . $searchTerm . '%');
            });
        }

        // Reply Status Filter
        $replyStatus = $request->input('reply_status', 'pending');
        if ($replyStatus === 'replied') {
            $query->whereNotNull('replied_at');
        } elseif ($replyStatus === 'pending') {
            $query->whereNull('replied_at');
        } // 'all' means no condition needed

        // Fetch messages with pagination and keep query string for pagination links
        $messages = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('admin.contacts.index', compact('messages'));
    }

    /**
     * Display the specified contact message.
     */
    public function show($id)
    {
        $message = Contact::findOrFail($id);
        return view('admin.contacts.show', compact('message'));
    }

    /**
     * Reply to the contact message.
     */
    public function reply(Request $request, $id)
    {
        $message = Contact::findOrFail($id);

        $request->validate([
            'reply_message' => 'required|string',
            'reply_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = [
            'reply_message' => $request->reply_message,
            'replied_at' => now(),
        ];

        if ($request->hasFile('reply_image')) {
            $image = $request->file('reply_image');
            $imageName = time() . '_reply_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/contacts'), $imageName);
            $data['reply_image'] = $imageName;
        }

        $message->update($data);

        return redirect()->back()->with('success', 'Reply saved and sent successfully.');
    }
    /**
     * Clear the admin reply from a contact message.
     */
    public function clearReply($id)
    {
        $message = Contact::findOrFail($id);

        // Delete the image file if it exists
        if ($message->reply_image && file_exists(public_path('images/contacts/' . $message->reply_image))) {
            unlink(public_path('images/contacts/' . $message->reply_image));
        }

        $message->update([
            'reply_message' => null,
            'reply_image' => null,
            'replied_at' => null,
        ]);

        return redirect()->back()->with('success', 'Admin reply cleared successfully.');
    }

    /**
     * Remove the specified contact message from storage.
     */
    public function destroy($id)
    {
        $message = Contact::findOrFail($id);
        $message->delete();

        return redirect()->route('admin.contacts.index')->with('success', 'Message deleted successfully.');
    }
}
