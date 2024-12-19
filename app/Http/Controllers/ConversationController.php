<?php

namespace App\Http\Controllers;

use App\Events\MessageEvent;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ConversationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $conversations = auth()->user()->conversations()
            ->with(['lastMessage', 'conversationUsers'])
            ->latest()
            ->get()
            ->map(function ($conversation) {
                return [
                    'id' => $conversation->id,
                    'name' => $conversation->name,
                    'type' => $conversation->type,
                    'last_message' => $conversation->lastMessage,
                    'unread_count' => $conversation->getUnreadCount(auth()->id()),
                    'participants' => $conversation->conversationUsers->map(function ($user) {
                        return [
                            'id' => $user->id,
                            'name' => $user->name,
                            'email' => $user->email,
                        ];
                    })
                ];
            });

        return Inertia::render('Conversation/chat', [
            'conversations' => $conversations
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required',
            'sender' => 'required',
            'body' => 'required',
        ]);

        $conversation = Conversation::findOrFail($request->conversation_id);
        
        // Check if user is participant
        abort_if(!$conversation->conversationUsers->contains('id', auth()->id()), 403);

        $message = new Message($request->only('sender', 'body'));
        $message = $conversation->messages()->save($message);

        // Mark as delivered for sender
        $message->markAsDelivered();

        broadcast(new MessageEvent($conversation, $message))->toOthers();

        return response()->json([
            'message' => $message,
            'sender' => auth()->user()
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $conversation = Conversation::with(['messages.sender', 'conversationUsers'])
            ->findOrFail($id);

        // Check if user is participant
        abort_if(!$conversation->conversationUsers->contains('id', auth()->id()), 403);

        // Mark all messages as read
        $conversation->messages()
            ->whereNotIn('sender', [auth()->id()])
            ->get()
            ->each(function ($message) {
                $message->markAsRead(auth()->id());
            });

        return Inertia::render('Conversation/chat', [
            'conversation' => [
                'id' => $conversation->id,
                'name' => $conversation->name,
                'type' => $conversation->type,
                'messages' => $conversation->messages->map(function ($message) {
                    return [
                        'id' => $message->id,
                        'body' => $message->body,
                        'sender' => $message->sender,
                        'status' => $message->status,
                        'created_at' => $message->created_at
                    ];
                }),
                'participants' => $conversation->conversationUsers->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                    ];
                })
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Create a new conversation
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function createConversation(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'type' => 'required|in:personal,group,support',
            'participants' => 'required|array|min:1',
            'participants.*' => 'exists:users,id'
        ]);

        $conversation = Conversation::create([
            'name' => $request->name,
            'type' => $request->type,
            'creator' => auth()->id()
        ]);

        // Add participants including the creator
        $participants = collect($request->participants)
            ->push(auth()->id())
            ->unique()
            ->toArray();
        
        $conversation->conversationUsers()->attach($participants);

        return response()->json($conversation->load('conversationUsers'));
    }

    /**
     * Mark conversation as read
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function markAsRead($id)
    {
        $conversation = Conversation::findOrFail($id);
        
        // Check if user is participant
        abort_if(!$conversation->conversationUsers->contains('id', auth()->id()), 403);

        $conversation->messages()
            ->whereNotIn('sender', [auth()->id()])
            ->get()
            ->each(function ($message) {
                $message->markAsRead(auth()->id());
            });

        return response()->json(['success' => true]);
    }

    /**
     * Add participants to a conversation
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function addParticipants(Request $request, $id)
    {
        $request->validate([
            'participants' => 'required|array|min:1',
            'participants.*' => 'exists:users,id'
        ]);

        $conversation = Conversation::findOrFail($id);
        
        // Only group conversations can add participants
        abort_if($conversation->type !== 'group', 403);
        
        // Only creator can add participants
        abort_if($conversation->creator !== auth()->id(), 403);

        $conversation->conversationUsers()->attach($request->participants);

        return response()->json($conversation->load('conversationUsers'));
    }

    /**
     * Remove participant from a conversation
     *
     * @param  int  $conversationId
     * @param  int  $userId
     * @return \Illuminate\Http\Response
     */
    public function removeParticipant($conversationId, $userId)
    {
        $conversation = Conversation::findOrFail($conversationId);
        
        // Only group conversations can remove participants
        abort_if($conversation->type !== 'group', 403);
        
        // Only creator can remove participants
        abort_if($conversation->creator !== auth()->id(), 403);

        $conversation->removeParticipant($userId);

        return response()->json(['success' => true]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * @param $targetUser
     * @return JsonResponse
     */
    public function get_active_conversation($targetUser): JsonResponse
    {
        return response()->json(conversation($targetUser));
    }
}
