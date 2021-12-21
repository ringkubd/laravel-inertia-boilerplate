<?php

namespace App\Http\Controllers;

use App\Models\Madrasha;
use App\Models\Teacher;
use App\Models\TeacherAttendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Ramsey\Uuid\Uuid;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TeacherAttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $madrasah = Madrasha::with('teacherAttendance')->get();
        $attendanceToday = TeacherAttendance::with('creator')->whereRaw("created_date = CURDATE()")->get();

        return Inertia::render('Teacher/Attendance/Index', [
            'madrasah' => $madrasah,
            'attendanceToday' => $attendanceToday
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $request->validate([
            'madrasah' => 'required',
            'date' => 'required'
        ]);
        $uid = Uuid::uuid4();
        $verificationUrl = route('teacher_attendance.show', $uid->toString());
        $qrCode = QrCode::format('png')->size(300)->generate($verificationUrl);
        $filePath = public_path("attendance/{$uid}.png");
        if (!file_exists(public_path("attendance"))) {
            mkdir(public_path("attendance"));
        }
        file_put_contents($filePath, $qrCode);

        $teacher = Teacher::where('madrashas_id', $request->madrasah)->get();
        dd($teacher);

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
