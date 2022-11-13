<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MadrasahResource;
use App\Http\Resources\TeachersAttendanceResource;
use App\Models\TeacherAttendanceLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\Image;

class TeacherAttendanceController extends Controller
{
    public function all(){
        $attendance = TeacherAttendanceLog::query()
            ->where('user_id', auth()->user()->id)
            ->get();
        return TeachersAttendanceResource::collection($attendance);
    }


    public function store(Request $request){
        $today = TeacherAttendanceLog::query()
            ->where('user_id', auth()->user()->id)
            ->whereRaw("date(login) = CURDATE()")->first();
        $todayDate = Carbon::now()->toDateString();
        $userId = auth()->user()->id;

        $image = base64_decode($request->image);
        $extension = getImageMimeType($image);

        if ($today){
            $safeName = "{$userId}_{$todayDate}_logout.".$extension;
            Storage::disk('public_path')->put("teacher_attendance/".$safeName, $image);
            $attendance = $today->update([
                'logout_location' =>  json_encode($request->coords),
                'logout' => Carbon::now(),
                'logout_photo' => $safeName,
            ]);

        }else{
            $safeName = "{$userId}_{$todayDate}_login.".$extension;
            Storage::disk('public_path')->put("teacher_attendance/".$safeName, $image);
            $attendance = TeacherAttendanceLog::create([
                'login_location' => json_encode($request->coords),
                'login' => Carbon::now(),
                'user_id' => auth()->user()->id,
                'login_photo' => $safeName,
            ]);
        }

        return response()->json(new TeachersAttendanceResource($attendance));
    }


    public function today(){
        $today = Carbon::now()->toDateString();
        $before30 = Carbon::now()->subDay(30)->toDateString();
        $attendance = TeacherAttendanceLog::query()
            ->where('user_id', auth()->user()->id)
            ->whereRaw("date(login) between '{$before30}' and '{$today}'")->latest()->get();
        $attendanceResource = TeachersAttendanceResource::collection($attendance);

        return $attendanceResource;
    }

    public function madrasah_location(){
        if (auth()->user()->has('madrasah')){
            $madrasah = auth()->user()->madrasah;
            return response()->json(new MadrasahResource($madrasah));
        }
    }
}
