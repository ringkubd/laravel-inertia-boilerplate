<?php

namespace App\Http\Controllers;

use App\Events\SupportEvent;
use App\Http\Resources\SupportMessageResource;
use App\Models\SupportConversation;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

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
        $conversation = $support->message()->create([
            'sender' => auth()->user()->id,
            'support_conversation_id' => $support->id,
            'message' => $request->message
        ]);
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
        return response()->json($conversation);
    }
}
