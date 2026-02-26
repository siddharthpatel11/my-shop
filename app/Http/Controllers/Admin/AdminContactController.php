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
    public function index()
    {
        $messages = Contact::orderBy('created_at', 'asc')->paginate(10);
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
     * Remove the specified contact message from storage.
     */
    public function destroy($id)
    {
        $message = Contact::findOrFail($id);
        $message->delete();

        return redirect()->route('admin.contacts.index')->with('success', 'Message deleted successfully.');
    }
}
