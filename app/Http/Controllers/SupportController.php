<?php

namespace App\Http\Controllers;

use App\Events\SupportEvent;
use App\Events\SupportOnlineEvent;
use App\Http\Resources\SupportMessageResource;
use App\Jobs\SupportNotificationJob;
use App\Models\SupportConversation;
use App\Models\SupportConversationMessage;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class SupportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        $support = SupportConversation::where('status', 0)->get();
        $activeConversation = SupportConversation::where('status', 0)->when($request->conversation_id, function ($q, $v) {
            $q->where('id', $v);
        })->first();

        return Inertia::render('Support/AdminChat', [
            'support' => $support,
            'activeConversation' => $activeConversation
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response|\Inertia\Response
     */
    public function create()
    {
        return Inertia::render('Support/Create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'conversation_id' => 'required',
                'message' => 'required|string',
                'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf,docx|max:2048'
            ]);
            $support = SupportConversation::findOrFail($request->conversation_id);

            $chat = [
                'sender' => auth()->user()->id,
                'support_conversation_id' => $support->id,
                'message' => $request->message
            ];

            if ($request->hasFile('attachment')) {
                $attachment = $request->file('attachment');
                $chat['attachment_type'] = $attachment->getClientMimeType();
                $fileName = $attachment->hashName();
                $filePath = $attachment->storeAs('conversations', $fileName, 'public');
                $chat['attachment'] = Storage::url($filePath);
            }

            $user = User::query()
                ->with('roles')
                ->where('email', 'mahadi@isdb-bisew.org')->get();

            $userArray = $user->pluck('id')->toArray();

            if (auth()->user()->id !== $support->creator) {
                $userArray[] = $support->creator;
            }

            $conversation = $support->message()->create($chat);

            Log::info('Message created', ['conversation' => $conversation]);

            broadcast(new SupportEvent(new SupportMessageResource($conversation)));
            Log::info('Event broadcasted', ['conversation' => $conversation->id]);

            return response()->json([
                'success' => true,
                'conversation' => new SupportMessageResource($conversation)
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating support message: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send message: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response|\Inertia\Response
     */
    public function show($id)
    {
        $conversation = SupportConversation::findOrFail($id);
        return Inertia::render('Support/Show', [
            'conversation' => $conversation,
            'messages' => $conversation->message,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response|\Inertia\Response
     */
    public function edit($id)
    {
        $conversation = SupportConversation::findOrFail($id);
        return Inertia::render('Support/Edit', [
            'conversation' => $conversation
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return true
     */
    public function update(Request $request, $id)
    {
        $support = SupportConversation::find($id);
        $support->update(['status' => 1]);
        return true;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        try {
            $conversation = SupportConversation::findOrFail($id);
            $conversation->delete();
            return redirect()->route('support.index')
                ->with('success', 'Support conversation deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting support conversation: ' . $e->getMessage());
            return redirect()->route('support.index')
                ->with('error', 'Failed to delete support conversation.');
        }
    }

    /**
     * @param $targetUser
     * @return JsonResponse
     */
    public function get_active_conversation(Request $request): JsonResponse
    {
        $conversation = auth()->user()->activeSupport;
        if (!$conversation) {
            $conversation = SupportConversation::create([
                'creator' => auth()->user()->id,
                'issues' => $request->issues,
                'status' => 0
            ]);
        }
        broadcast(new SupportOnlineEvent(auth()->user()));
        return response()->json($conversation);
    }

    /**
     * Delete a message from a support conversation
     *
     * @param SupportConversationMessage $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteMessage(SupportConversationMessage $message)
    {
        try {
            // Check permission
            if ($message->sender != auth()->id() && !auth()->user()->hasRole(['Admin', 'Super Admin'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized to delete this message'
                ], 403);
            }

            // Delete attachment if exists
            if ($message->attachment) {
                $path = str_replace('/storage/', '', $message->attachment);
                Storage::disk('public')->delete($path);
            }

            // Delete the message
            $message->delete();

            return response()->json([
                'success' => true,
                'message' => 'Message deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting message: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete message'
            ], 500);
        }
    }
}
