<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Models\ClassRoom;
use App\Models\Notice;
use App\Models\Student;
use App\Notifications\AppNotification;
use Illuminate\Http\Request;
use Inertia\Inertia;
use function Clue\StreamFilter\fun;

class NoticeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $notices = Notice::query()
            ->with('classRoom', 'seen')
            ->where("published_at", '<=', now())
            ->latest()
            ->paginate();
        return Inertia::render('Notice/Index', [
            'notices' => $notices,
            'can' => [
                'create' => auth()->user()->can('create_notice'),
                'delete' => auth()->user()->can('delete_notice'),
                'view' => auth()->user()->can('view_notice'),
            ]
        ]);
    }

    /**
     * @return void
     */

    public function create(){
        $sessions = AcademicSession::all();
        $classRoom = ClassRoom::all();
        return Inertia::render('Notice/Create', [
            'notice' => new Notice(),
            'sessions' => $sessions,
            'class_rooms' => $classRoom
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
            'title' => 'required',
            'body' => 'required'
        ]);
        $notice = Notice::create($request->all());
        $students = Student::query()
            ->with('classroom')
            ->when($request->class_room_id, function ($q)use($request){
                $q->whereHas('classroom', function ($q) use ($request){
                    $q->where('class_rooms.id', $request->class_room_id);
                });
            })
            ->when($request->academic_session, function ($q)use($request){
                $q->where('polytechnic_session', $request->academic_session);
            })
            ->with('users')
            ->get();
        foreach ($students as $student){
            try{
                if ($student->users)
                    $student->users->notify(new AppNotification($notice));
            }catch(Execption $e){
                dd($e->getMessage());
            }

        }
        return redirect()->route('notice.index')->withSuccess("Successfully added.");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Notice  $notice
     * @return \Illuminate\Http\Response
     */
    public function show(Notice $notice)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Notice  $notice
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Notice $notice)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Notice  $notice
     * @return \Illuminate\Http\Response
     */
    public function destroy(Notice $notice)
    {
        $notice->delete();
        return redirect()->route('notice.index')->withSuccess("Successfully deleted.");
    }
}
