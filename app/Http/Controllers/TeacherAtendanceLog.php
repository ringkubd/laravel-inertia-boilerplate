<?php

namespace App\Http\Controllers;

use App\Http\Resources\TeacherResource;
use App\Http\Resources\TeachersAttendanceResource;
use App\Models\Teacher;
use App\Models\TeacherAttendanceLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TeacherAtendanceLog extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $today = $request->today ?? Carbon::now()->toDateString();
        $attendance = Teacher::query()
            ->with(['attendanceLogOneDay' => function($q) use($today){
                $q->whereRaw("date(login) = '{$today}'");
            }])
            ->get();

        return Inertia::render('Teacher/Attendance/AppAttendance', [
            'attendances' => TeacherResource::collection($attendance),
            'can' => [
                'create' => auth()->user()->can('create_app_attendance'),
                'update' => auth()->user()->can('update_app_attendance'),
                'delete' => auth()->user()->can('delete_app_attendance'),
                'view' => auth()->user()->can('view_app_attendance'),
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $teacherAttendanceLog = TeacherAttendanceLog::find($id);
        return Inertia::render('Teacher/Attendance/AttendanceMap', [
            'attendance' => new TeachersAttendanceResource($teacherAttendanceLog)
        ]);
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
}
