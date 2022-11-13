<?php

namespace App\Http\Controllers;

use App\Http\Resources\TeachersAttendanceResource;
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
    public function index()
    {
        $today = Carbon::now()->toDateString();
        $before30 = Carbon::now()->subDay(30)->toDateString();
        $attendance = TeacherAttendanceLog::query()
            ->whereRaw("date(login) between '{$before30}' and '{$today}'")->latest()->get();
        return Inertia::render('Teacher/Attendance/AppAttendance', [
            'attendances' => TeachersAttendanceResource::collection($attendance),
            'can' => [
                'create' => auth()->user()->can('create_fee'),
                'update' => auth()->user()->can('update_fee'),
                'delete' => auth()->user()->can('delete_fee'),
                'view' => auth()->user()->can('view_fee'),
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
}
