<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactMessageReceived;

class ContactMessageController extends Controller
{
    // Store new contact message
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string|min:10|max:2000',
        ]);

        try {
            $contactMessage = ContactMessage::create($validated);

            // Send email notification (optional)
            // Mail::to('admin@example.com')->send(new ContactMessageReceived($contactMessage));

            return redirect()->back()->with('success', 'Thank you for your message! We will get back to you soon.');

        } catch (\Exception $e) {
            \Log::error('Contact message error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Sorry, there was an error sending your message. Please try again.');
        }
    }

    // Admin index - list all messages
    public function index()
    {
        $messages = ContactMessage::latest()->paginate(10);
        $unreadCount = ContactMessage::unread()->count();

        return view('admin.contact-messages.index', compact('messages', 'unreadCount'));
    }

    // Show specific message
    public function show(ContactMessage $contactMessage)
    {
        // Mark as read when viewed
        if ($contactMessage->status === 'unread') {
            $contactMessage->markAsRead();
        }

        return view('admin.contact-messages.show', compact('contactMessage'));
    }

    // Mark as replied
    public function markAsReplied(Request $request, ContactMessage $contactMessage)
    {
        $validated = $request->validate([
            'admin_notes' => 'required|string|min:10|max:1000',
        ]);

        $contactMessage->markAsReplied($validated['admin_notes']);

        return redirect()->route('admin.contact-messages.show', $contactMessage)
            ->with('success', 'Message marked as replied successfully.');
    }

    // Delete message
    public function destroy(ContactMessage $contactMessage)
    {
        $contactMessage->delete();

        return redirect()->route('admin.contact-messages.index')
            ->with('success', 'Message deleted successfully.');
    }

    // Get message statistics for dashboard
    public function getMessageStats()
    {
        return [
            'total_messages' => ContactMessage::count(),
            'unread_messages' => ContactMessage::unread()->count(),
            'replied_messages' => ContactMessage::replied()->count(),
            'recent_messages' => ContactMessage::latest()->take(5)->get(),
        ];
    }
}
