<?php

namespace App\Http\Controllers;

use App\Events\TicketMessageCreated;
use App\Models\Attachment;
use App\Models\SupportTicket;
use App\Models\TicketMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TicketMessageController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Store a newly created message in storage.
     */
    public function store(Request $request, SupportTicket $ticket)
    {
        $user = Auth::user();
        $isAdmin = $user->hasAnyRole(['Admin', 'Super Admin', 'Account']);
        $isOwner = $ticket->user_id === $user->id;

        // Check authorization
        if (!$isAdmin && !$isOwner) {
            abort(403, 'You are not authorized to reply to this ticket.');
        }

        // Validate input
        $validated = $request->validate([
            'message' => 'required|string|max:5000',
            'sender_type' => 'required|in:user,admin',
            'attachment' => 'nullable|file|max:10240|mimes:jpeg,png,jpg,pdf,doc,docx',
        ]);

        // Verify sender_type matches user role
        if ($validated['sender_type'] === 'admin' && !$isAdmin) {
            abort(403, 'Only admins can send admin replies.');
        }

        if ($validated['sender_type'] === 'user' && !$isOwner) {
            abort(403, 'Only the ticket owner can send user replies.');
        }

        // Create message
        $message = TicketMessage::create([
            'support_ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'message' => $validated['message'],
            'sender_type' => $validated['sender_type'],
        ]);

        // Handle attachment
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $path = $file->store('ticket-messages', 'public');

            Attachment::create([
                'attachable_id' => $message->id,
                'attachable_type' => TicketMessage::class,
                'filename' => $file->getClientOriginalName(),
                'filepath' => Storage::url($path),
                'mimetype' => $file->getMimeType(),
                'filesize' => $file->getSize(),
                'uploaded_by' => $user->id,
            ]);

            $message->load('attachments');
        }

        // Load user relationship
        $message->load('user');

        // Broadcast event
        TicketMessageCreated::dispatch($message);

        // Auto-update ticket status if replying as admin
        if ($validated['sender_type'] === 'admin' && $ticket->status === 'open') {
            $ticket->update(['status' => 'in_progress']);
        }

        return response()->json([
            'message' => $message,
            'ticket' => $ticket,
        ]);
    }

    /**
     * Delete a message (only the author can delete).
     */
    public function destroy(TicketMessage $message)
    {
        $user = Auth::user();

        // Check authorization
        if ($message->user_id !== $user->id) {
            abort(403, 'You are not authorized to delete this message.');
        }

        // Delete attachments
        $message->attachments()->forceDelete();

        // Soft delete the message
        $message->delete();

        return response()->json([
            'message' => 'Message deleted successfully.'
        ]);
    }
}
