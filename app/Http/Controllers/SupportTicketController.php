<?php

/**
 * ============================================================================
 * SUPPORT TICKET CONTROLLER GENERATION SCRIPT
 * ============================================================================
 *
 * This script generates the complete SupportTicketController with all necessary
 * methods for handling support tickets with real-time functionality.
 *
 * Usage: Replace the methods in SupportTicketController with the code below.
 *
 * ============================================================================
 */

namespace App\Http\Controllers;

use App\Events\TicketAssigned;
use App\Events\TicketCreated;
use App\Events\TicketMessageCreated;
use App\Events\TicketStatusUpdated;
use App\Models\Attachment;
use App\Models\SupportTicket;
use App\Models\TicketMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class SupportTicketController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Display a listing of the support tickets.
     * Users see only their own tickets, Admins see all.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $isAdmin = $user->hasAnyRole(['Admin', 'Super Admin', 'Account']);

        // Build base query
        $query = SupportTicket::with(['user', 'assignedTo', 'messages'])
            ->withCount('messages');

        // Apply filters based on user role
        if (!$isAdmin) {
            $query->where('user_id', $user->id);
        }

        // Apply status filter
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Apply assigned filter (admin only)
        if ($isAdmin && $request->filled('assigned_to')) {
            if ($request->input('assigned_to') === 'unassigned') {
                $query->whereNull('assigned_to');
            } else {
                $query->where('assigned_to', $request->input('assigned_to'));
            }
        }

        // Apply sorting
        $sort = $request->input('sort', '-created_at');
        if ($sort === '-created_at') {
            $query->orderBy('created_at', 'desc');
        } elseif ($sort === 'created_at') {
            $query->orderBy('created_at', 'asc');
        } elseif ($sort === '-updated_at') {
            $query->orderBy('updated_at', 'desc');
        }

        $tickets = $query->paginate(15);

        // Get admin list for assignment dropdown
        $admins = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['Admin', 'Super Admin', 'Account']);
        })->get(['id', 'name']);

        if ($request->wantsJson()) {
            return response()->json([
                'tickets' => $tickets->items(),
                'pagination' => [
                    'total' => $tickets->total(),
                    'per_page' => $tickets->perPage(),
                    'current_page' => $tickets->currentPage(),
                    'last_page' => $tickets->lastPage(),
                    'from' => $tickets->firstItem(),
                    'to' => $tickets->lastItem(),
                    'prev_page_url' => $tickets->previousPageUrl(),
                    'next_page_url' => $tickets->nextPageUrl(),
                ]
            ]);
        }

        return Inertia::render('Support/TicketsList', [
            'initialTickets' => $tickets->items(),
            'pagination' => [
                'total' => $tickets->total(),
                'per_page' => $tickets->perPage(),
                'current_page' => $tickets->currentPage(),
                'last_page' => $tickets->lastPage(),
                'from' => $tickets->firstItem(),
                'to' => $tickets->lastItem(),
                'prev_page_url' => $tickets->previousPageUrl(),
                'next_page_url' => $tickets->nextPageUrl(),
            ]
        ]);
    }

    /**
     * Show the form for creating a new support ticket.
     */
    public function create()
    {
        return Inertia::render('Support/CreateTicket');
    }

    /**
     * Store a newly created support ticket in storage.
     */
    public function store(Request $request)
    {
        // Check if user can create tickets
        $user = $request->user();
        $canCreate = $user->hasAnyRole(['Student', 'Instructor', 'Lab Attendant', 'Admin', 'Super Admin', 'Account']);

        if (!$canCreate) {
            abort(403, 'You are not authorized to create support tickets.');
        }

        // Validate input
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string|max:5000',
            'attachments.*' => 'nullable|file|max:10240|mimetypes:image/jpeg,image/png,image/jpg,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ]);

        // Create ticket
        $ticket = SupportTicket::create([
            'user_id' => $user->id,
            'subject' => $validated['subject'],
            'description' => $validated['description'],
            'status' => 'open',
        ]);

        // Handle attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('tickets', 'public');

                Attachment::create([
                    'attachable_id' => $ticket->id,
                    'attachable_type' => SupportTicket::class,
                    'filename' => $file->getClientOriginalName(),
                    'filepath' => Storage::url($path),
                    'mimetype' => $file->getMimeType(),
                    'filesize' => $file->getSize(),
                    'uploaded_by' => $user->id,
                ]);
            }
        }

        // Broadcast event
        TicketCreated::dispatch($ticket->load(['user', 'attachments']));

        if ($request->wantsJson()) {
            return response()->json([
                'ticket' => $ticket,
                'message' => 'Support ticket created successfully.'
            ], 201);
        }

        return redirect()->route('support-tickets.show', $ticket)->with('success', 'Support ticket created successfully.');
    }

    /**
     * Display the specified support ticket.
     */
    public function show(Request $request, SupportTicket $supportTicket)
    {
        $user = $request->user();
        $isAdmin = $user->hasAnyRole(['Admin', 'Super Admin', 'Account']);
        $isOwner = $supportTicket->user_id === $user->id;

        // Check authorization
        if (!$isAdmin && !$isOwner) {
            abort(403, 'You are not authorized to view this ticket.');
        }

        // Load relationships
        $supportTicket->load(['user', 'assignedTo', 'attachments']);
        $messages = $supportTicket->messages()
            ->with(['user', 'attachments'])
            ->orderBy('created_at', 'asc')
            ->get();

        // Get admin list
        $admins = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['Admin', 'Super Admin', 'Account']);
        })->get(['id', 'name']);

        return Inertia::render('Support/TicketDetail', [
            'ticket' => $supportTicket,
            'messages' => $messages,
            'admins' => $admins,
        ]);
    }

    /**
     * Update the specified support ticket in storage.
     */
    public function update(Request $request, SupportTicket $supportTicket)
    {
        $user = $request->user();
        $isAdmin = $user->hasAnyRole(['Admin', 'Super Admin', 'Account']);

        // Check authorization
        if (!$isAdmin) {
            abort(403, 'You are not authorized to update this ticket.');
        }

        // Validate input
        $validated = $request->validate([
            'status' => 'nullable|in:open,in_progress,closed',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $oldStatus = $supportTicket->status;

        // Update status if provided
        if ($request->filled('status') && $oldStatus !== $validated['status']) {
            $supportTicket->update(['status' => $validated['status']]);
            TicketStatusUpdated::dispatch($supportTicket->fresh(['user', 'assignedTo']), $oldStatus);
        }

        // Update assignment if provided (including null to unassign)
        if ($request->has('assigned_to')) {
            $supportTicket->update(['assigned_to' => $validated['assigned_to']]);
            $supportTicket->load(['user', 'assignedTo']);
            TicketAssigned::dispatch($supportTicket);
        }

        // Reload relationships
        $supportTicket->load(['user', 'assignedTo', 'messages']);

        return response()->json([
            'ticket' => $supportTicket,
            'message' => 'Ticket updated successfully.'
        ]);
    }

    /**
     * Remove the specified support ticket from storage.
     */
    public function destroy(Request $request, SupportTicket $supportTicket)
    {
        $user = $request->user();
        $isAdmin = $user->hasAnyRole(['Admin', 'Super Admin', 'Account']);
        $isOwner = $supportTicket->user_id === $user->id;

        // Check authorization
        if (!$isAdmin && !$isOwner) {
            abort(403, 'You are not authorized to delete this ticket.');
        }

        // Delete attachments
        $supportTicket->attachments()->forceDelete();
        $supportTicket->messages()->forceDelete();

        // Soft delete the ticket
        $supportTicket->delete();

        return redirect()->route('support-tickets.index')->with('success', 'Support ticket deleted successfully.');
    }

    /**
     * Display admin dashboard for all support tickets.
     */
    public function adminDashboard(Request $request)
    {
        $user = $request->user();

        // Check if user is admin
        if (!$user->hasAnyRole(['Admin', 'Super Admin', 'Account'])) {
            abort(403, 'You are not authorized to access this page.');
        }

        // Get all tickets
        $query = SupportTicket::with(['user', 'assignedTo', 'messages'])
            ->withCount('messages');

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('assigned_to')) {
            if ($request->input('assigned_to') === 'unassigned') {
                $query->whereNull('assigned_to');
            } else {
                $query->where('assigned_to', $request->input('assigned_to'));
            }
        }

        // Apply sorting
        $sort = $request->input('sort', '-created_at');
        if ($sort === '-created_at') {
            $query->orderBy('created_at', 'desc');
        } elseif ($sort === 'created_at') {
            $query->orderBy('created_at', 'asc');
        } elseif ($sort === '-updated_at') {
            $query->orderBy('updated_at', 'desc');
        }

        $tickets = $query->paginate(20);

        // Get admin list
        $admins = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['Admin', 'Super Admin', 'Account']);
        })->get(['id', 'name']);

        if ($request->wantsJson()) {
            return response()->json([
                'tickets' => $tickets->items(),
                'pagination' => [
                    'total' => $tickets->total(),
                    'per_page' => $tickets->perPage(),
                    'current_page' => $tickets->currentPage(),
                    'last_page' => $tickets->lastPage(),
                    'from' => $tickets->firstItem(),
                    'to' => $tickets->lastItem(),
                    'prev_page_url' => $tickets->previousPageUrl(),
                    'next_page_url' => $tickets->nextPageUrl(),
                ]
            ]);
        }

        return Inertia::render('Support/AdminTickets', [
            'initialTickets' => $tickets->items(),
            'pagination' => [
                'total' => $tickets->total(),
                'per_page' => $tickets->perPage(),
                'current_page' => $tickets->currentPage(),
                'last_page' => $tickets->lastPage(),
                'from' => $tickets->firstItem(),
                'to' => $tickets->lastItem(),
                'prev_page_url' => $tickets->previousPageUrl(),
                'next_page_url' => $tickets->nextPageUrl(),
            ],
            'admins' => $admins,
        ]);
    }
}
