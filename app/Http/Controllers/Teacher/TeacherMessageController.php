<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\Teacher\TeacherMessage;
use App\Models\User;
use App\Notifications\Teacher\TeacherMessageNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Kutia\Larafirebase\Messages\FirebaseMessage;

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
        $teachers = Teacher::query()
            ->selectRaw('users_id as id, name as text')
            ->where('users_id', '!=', auth()->user()->id)
            ->get();
        return Inertia::render('Teacher/Message/Create', [
            'teachers' => $teachers
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
        $send_by = auth()->user()->id;
        $send_to = User::find($request->to);
        $token = $send_to->firebase_token;
        $message = $request->message;

        $existing_conversation = TeacherMessage::query()
            ->where(function ($q) use($send_to){
                $q->where('from', $send_to->id)->orWhere('to', $send_to->id);
            })
            ->where(function ($q) use($send_by){
                $q->where('from', $send_by)->orWhere('to', $send_by);
            })
            ->first();
        if ($existing_conversation == null){
            $conversation_id = uiidv5($send_by.'_'.$send_to->id);
            $conversation = TeacherMessage::create([
                'from' => $send_by,
                'to' => $send_to->id,
                'message' => $message,
                'conversation_id' => $conversation_id
            ]);
        }else{
            $conversation = TeacherMessage::create([
                'from' => $send_by,
                'to' => $send_to->id,
                'message' => $message,
                'conversation_id' => $existing_conversation->conversation_id
            ]);
        }
        if ($request->hasFile('attachment')){
            $fileName = Carbon::now()->timestamp .'.'.$request->file('attachment')->getExtension();
            $request->file('attachment')->move(public_path('teacher_conversation_attachment'), $fileName);
            $conversation->attachments()->create($fileName);
        }
        $title = strlen($message) > 50 ? substr($request->message,0,50)."..." : $message;
//        $abc = $send_to->notify(new TeacherMessageNotification($token, $title, $message));
        $firebase = (new FirebaseMessage())
            ->withTitle($title)
            ->withBody($message)
            ->withPriority('high')
            ->withAdditionalData([
                'user' => auth()->user()
            ]);
        $message = $firebase->asMessage($token);
        $notice = $firebase->asNotification($token);

        if ($request->is('api/*')){
            return response()->json([
                'success' => true,
                'data' => $message
            ]);
        }
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

    public function conversation(){
        $conversations = TeacherMessage::query()
            ->selectRaw('teacher_messages.id, teacher_messages.to, teacher_messages.from, conversation_id, message, teacher_messages.created_at,teacher_messages.updated_at, u.name as from_name, u2.name as to_name')
            ->join('users as u', 'teacher_messages.from', 'u.id')
            ->join('users as u2', 'teacher_messages.to', 'u.id')
            ->when(auth()->user()->hasAnyRole('Instructor', 'Lab Attendant', 'Super', 'Student'), function ($q){
                $q->where('to', auth()->user()->id)->orWhere('from', auth()->user()->id);
            })
            ->where('type', 'Single')
            ->get()
            ->groupBy('conversation_id');

        if (\request()->is('api/*')){
            return response()->json([
                'success' => true,
                'data' => $conversations
            ]);
        }
        return Inertia::render('Teacher/Message/Index', [
            'conversations' => $conversations,
            'can' => [
                'create' => auth()->user()->hasrole('Super Admin'),
                'update' => auth()->user()->hasrole('Super Admin'),
                'delete' => auth()->user()->hasrole('Super Admin'),
                'view' => auth()->user()->hasrole('Super Admin'),
            ]
        ]);
    }

    public function conversationMessage($conversation_id){
        $teacher_messages = TeacherMessage::query()
            ->where('conversation_id', $conversation_id)
            ->with(['sendBy' => fn($u) => $u->with(['teacher' => fn($t) => $t->select('photo')])->select('id', 'name')])
            ->latest()
            ->get();
        if (\request()->is('api/*')){
            return response()->json([
                'success' => true,
                'data' => $teacher_messages
            ]);
        }
    }

}
