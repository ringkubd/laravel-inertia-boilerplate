<?php

namespace App\Http\Controllers;

use App\Events\SupportEvent;
use App\Events\SupportOnlineEvent;
use App\Http\Resources\SupportMessageResource;
use App\Models\SupportConversation;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use function Sodium\randombytes_random16;

class SupportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
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
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            'conversation_id' => 'required'
        ]);
        $support = SupportConversation::find($request->conversation_id);

        $chat = [
            'sender' => auth()->user()->id,
            'support_conversation_id' => $support->id,
            'message' => $request->message
        ];

        if ($request->hasFile('attachment')) {
            $attachment = $request->file('attachment');
            $chat['attachment_type'] = $attachment->getClientMimeType();
            $fileName = randombytes_random16().".".$attachment->getClientOriginalExtension();
            $filePath = "conversation/".$fileName;
            $chat['attachment'] = url($filePath);
            $attachment->move(public_path("conversation/"), $fileName);
        }

        $conversation = $support->message()->create($chat);
        broadcast(new SupportEvent(new SupportMessageResource($conversation)));
        return response()->json([
            'successs' => true,
            'conversation' => new SupportMessageResource($conversation)
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
        //
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
}
