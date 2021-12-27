<?php

namespace App\Http\Controllers;

use App\Models\Madrasha;
use App\Models\Teacher;
use App\Models\TeacherAttendance;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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
    public function index(Request $request)
    {
        $date = $request->date ?? Carbon::now()->format('Y-m-d');

        $madrasah = Madrasha::with(['teacherAttendance' => function ($q) use ($date){
            $q->with('creator')->where('created_date', $date);
        }])->get();

        $attendanceToday = TeacherAttendance::with('creator')
            ->when($request->date, function ($q, $v){
                $q->where('created_date', "$v");
            })
            ->whereRaw("created_date = CURDATE()")->get();

        $permissionOld = auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin');

        return Inertia::render('Teacher/Attendance/Index', [
            'madrasah' => $madrasah,
            'attendanceToday' => $attendanceToday,
            'permission' => $permissionOld
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
        $permissionOld = auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin');
        if ($request->date != Carbon::today()->format('Y-m-d') && !$permissionOld) abort(401);

        $teacherAttendance = TeacherAttendance::where('created_date', $request->date)->where('madrasha_id', $request->madrasah)->first();
        if (!$teacherAttendance && $request->date == now()->format('Y-m-d')) {
            $uid = Uuid::uuid4();
            $verificationUrl = route('teacher_attendance.verify', $uid->toString());
            $qrCode = QrCode::format('png')->size(300)->generate($verificationUrl);
            $qrImage = "/attendance/{$uid}.png";
            $filePath = public_path("attendance/{$uid}.png");
            if (!file_exists(public_path("attendance"))) {
                mkdir(public_path("attendance"));
            }
            file_put_contents($filePath, $qrCode);

            $teacherAttendance = TeacherAttendance::create([
                'document_uri' => $uid->toString(),
                'madrasha_id' => $request->madrasah,
                'creator_id' => auth()->user()->id,
                'created_date' => $request->date
            ]);

        }else if ($teacherAttendance){
            $uid = $teacherAttendance->document_uri;
            $qrImage = "/attendance/{$uid}.png";
            if (!file_exists($qrImage)) {
                $verificationUrl = route('teacher_attendance.verify', $uid);
                $qrCode = QrCode::format('png')->size(300)->generate($verificationUrl);
                $filePath = public_path("attendance/{$uid}.png");
                if (!file_exists(public_path("attendance"))) {
                    mkdir(public_path("attendance"));
                }
                file_put_contents($filePath, $qrCode);
            }
        }else{
            return abort(404);
        }


        $teacher = Teacher::with('madrasa', 'trade')->where('madrashas_id', $request->madrasah)->get();
        $madrasah = Madrasha::where('id', $request->madrasah)->first();

        $trade = $teacher?->pluck('trade')?->unique('name')?->pluck('name')?->toArray() ?? [];
        $tradeString = implode(', ', $trade);

        return Inertia::render('Teacher/Attendance/AttendanceSheet', [
            'teacher' => $teacher,
            'qr_code' => $qrImage,
            'madrasah' => $madrasah,
            'trades' => $tradeString
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

    public function verify($uid){
        $teacher_attendance = TeacherAttendance::with('madrasah', 'creator')->where('document_uri', $uid)->first();
        return Inertia::render('Teacher/Attendance/Verify', [
            'verification' => $teacher_attendance
        ]);
    }
}
