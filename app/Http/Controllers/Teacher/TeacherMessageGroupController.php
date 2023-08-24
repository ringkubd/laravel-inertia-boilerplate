<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Teacher\TeacherMessage;
use App\Models\Teacher\TeacherMessageGroup;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Kutia\Larafirebase\Facades\Larafirebase;
use Kutia\Larafirebase\Messages\FirebaseMessage;

class TeacherMessageGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $group = TeacherMessageGroup::query()
            ->with('members')
            ->with(['messages' => fn($m) => $m->with(['sendBy' => fn($s) => $s->with(['teacher' => fn($t) => $t->select('photo')])->select('id', 'name')])])
            ->whereHas('members', function ($q){
                $q->where('users.id', auth()->user()->id);
            })
            ->get();
        if (request()->is('api/*')){
            return response()->json([
                'success' => 'true',
                'data' => $group
            ]);
        }
        return response()->json([
            'success' => 'true',
            'data' => $group
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validate = Validator::make($request->all(),[
            'group_id' => 'required',
            'message' => 'required'
        ]);
        if ($validate->failed()){
           if ($request->is('api/*')){
               return response()->json([
                   'success' => false,
                   'data' => $validate->getMessageBag()
               ]);
           }
        }
        $message = $request->message;
        $title = strlen($message) > 50 ? substr($message,0,50)."..." : $message;
        $send_by = auth()->user()->id;

        $group = TeacherMessageGroup::query()
            ->where('id', $request->group_id)
            ->with('members', 'messages')
            ->whereHas('members', function ($q){
                $q->where('users.id', auth()->user()->id);
            })
            ->first();
        $members = $group->members;
        $tokens = $members->filter(fn($u) => $u->id !== auth()->user()->id)->pluck('firebase_token')->filter()->toArray();
//        $tokens = $members->pluck('firebase_token')->filter()->toArray();
//        return implode(',',$tokens);

        $conversation = TeacherMessage::create([
            'from' => $send_by,
            'message' => $message,
            'conversation_id' => $group->conversation_id,
            'type' => 'Group'
        ]);

        if ($request->hasFile('attachment')){
            $fileName = Carbon::now()->timestamp .'.'.$request->file('attachment')->getExtension();
            $request->file('attachment')->move(public_path('teacher_conversation_attachment'), $fileName);
            $conversation->attachments()->create($fileName);
        }
        $firebase = (new FirebaseMessage())
            ->withTitle($title)
            ->withBody($message)
            ->withAdditionalData([
                'user' => [
                    'id' => auth()->user()->id,
                    'name' => auth()->user()->name,
                    'photo' => auth()->user()?->teacher?->photo
                ],
                'conversation_id' => $group->conversation_id,
                'message_id' => $conversation->id,
            ]);
        foreach ($tokens as $token){
            $message = $firebase->asMessage($token);
        }
//        $notice = $firebase->asNotification($tokens);
        return $message->body();
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
     * @param  \App\Models\Teacher\TeacherMessageGroup  $teacherMessageGroup
     * @return \Illuminate\Http\Response
     */
    public function show(TeacherMessageGroup $teacherMessageGroup)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Teacher\TeacherMessageGroup  $teacherMessageGroup
     * @return \Illuminate\Http\Response
     */
    public function edit(TeacherMessageGroup $teacherMessageGroup)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Teacher\TeacherMessageGroup  $teacherMessageGroup
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TeacherMessageGroup $teacherMessageGroup)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Teacher\TeacherMessageGroup  $teacherMessageGroup
     * @return \Illuminate\Http\Response
     */
    public function destroy(TeacherMessageGroup $teacherMessageGroup)
    {
        //
    }
}
