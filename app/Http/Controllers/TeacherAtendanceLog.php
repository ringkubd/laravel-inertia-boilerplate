<?php

namespace App\Http\Controllers;

use App\Http\Resources\TeacherResource;
use App\Http\Resources\TeachersAttendanceResource;
use App\Models\Madrasha;
use App\Models\Teacher;
use App\Models\TeacherAttendanceLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TeacherAtendanceLog extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
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
            'attendance' => new TeachersAttendanceResource($teacherAttendanceLog),
            'logout' => false
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

    public function monthly_attendance(){
        $month = request()->has('month') && request()->month != "" && request()->month != 'undefined' ? \request()->month : Carbon::now()->month;
        $year = request()->has('year') && request()->year != "" && request()->year != 'undefined' ? \request()->year : Carbon::now()->year;
        $first_day = Carbon::parse("$year-$month-01")->toDateString();
        $last_day =  Carbon::parse($first_day)->lastOfMonth()->toDateString();
        $attendance = TeacherAttendanceLog::query()
            ->selectRaw('user_id, login, logout, login_location, login_photo, logout_location, logout_photo, date(login) as attn_date')
            ->whereRaw("date(login) between '$first_day' and '$last_day'")
            ->when(\request()->madrasha_id, function ($q, $v){
                $q->whereHas('user', function ($q) use($v){
                    $q->where('madrasha_id', $v);
                });
            })
            ->get();
        $madrasah_list = Madrasha::all();
        $teachers = Teacher::query()->when(\request()->madrasha_id, function ($q, $v){
            $q->where('madrashas_id', $v);
        })->get();
        $data = \request()->all();
        $data['year'] = $year;
        $data['month'] = $month;

        return Inertia::render('Teacher/Attendance/MonthlyAttendance', [
            'attendances' => TeachersAttendanceResource::collection($attendance)->groupBy("user_id"),
            'dates' => array_unique($attendance->pluck("attn_date")->toArray()),
            'teachers' => $teachers,
            'madrasahs' => $madrasah_list,
            'request' => $data
        ]);
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function logoutLocation($id)
    {
        $teacherAttendanceLog = TeacherAttendanceLog::find($id);
        return Inertia::render('Teacher/Attendance/AttendanceMap', [
            'attendance' => new TeachersAttendanceResource($teacherAttendanceLog),
            'logout' => true
        ]);
    }
}
