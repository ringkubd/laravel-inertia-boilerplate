<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Teacher\TeacherMessage;
use App\Models\User;
use App\Notifications\Teacher\TeacherMessageNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TeacherMessageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

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
        $send_by = auth()->user()->id;
        $send_to = $request->to;
        $token = $request->firebase_token;
        $message = $request->message;

        $existing_conversation = TeacherMessage::query()
            ->where(function ($q) use($send_to){
                $q->where('from', $send_to)->orWhere('to', $send_to);
            })
            ->where(function ($q) use($send_by){
                $q->where('from', $send_by)->orWhere('to', $send_by);
            })
            ->first();
        if ($existing_conversation == null){
            $conversation_id = random_bytes(64);
            $conversation = TeacherMessage::create([
                'from' => $send_by,
                'to' => $send_to,
                'message' => $message,
                'conversation_id' => $conversation_id
            ]);
        }else{
            $conversation = TeacherMessage::create([
                'from' => $send_by,
                'to' => $send_to,
                'message' => $message,
                'conversation_id' => $existing_conversation->conversation_id
            ]);
        }
        if ($request->hasFile('attachment')){
            $fileName = Carbon::now()->timestamp .'.'.$request->file('attachment')->getExtension();
            $request->file('attachment')->move(public_path('teacher_conversation_attachment'), $fileName);
            $conversation->attachments()->create($fileName);
        }
        $to = User::find($send_to);
        $out = strlen($message) > 50 ? substr($request->message,0,50)."..." : $message;
        $to->notify(new TeacherMessageNotification($token, $out, $message));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Teacher\TeacherMessage  $teacherMessage
     * @return \Illuminate\Http\Response
     */
    public function show(TeacherMessage $teacherMessage)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Teacher\TeacherMessage  $teacherMessage
     * @return \Illuminate\Http\Response
     */
    public function edit(TeacherMessage $teacherMessage)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Teacher\TeacherMessage  $teacherMessage
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TeacherMessage $teacherMessage)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Teacher\TeacherMessage  $teacherMessage
     * @return \Illuminate\Http\Response
     */
    public function destroy(TeacherMessage $teacherMessage)
    {
        //
    }

    /**
     * @param Request $request
     * @return void
     */
    public function storeFcmToken(Request $request){
        $validation = Validator::make($request->all(), [
            'firebase_token' => 'required'
        ]);
        auth()->user()->update([
            'firebase_token' => $request->firebase_token
        ]);
       return [
           'status' => 'success',
           'data' => auth()->user()
       ];
    }
}
