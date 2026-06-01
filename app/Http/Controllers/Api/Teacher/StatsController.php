<?php

namespace App\Http\Controllers\Api\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\StudentAttendance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatsController extends Controller
{
    public function index()
    {
        $teacherId = auth()->user()->teacher?->id ?? auth()->id();

        $classes = ClassRoom::where('teacher_id', $teacherId)->get();
        $classIds = $classes->pluck('id');

        $studentIds = DB::table('class_room_students')
            ->whereIn('class_room_id', $classIds)
            ->pluck('student_id')
            ->unique();

        $todayDate = Carbon::now()->toDateString();
        $dayName = Carbon::now()->format('D');
        $todayClasses = $classes->filter(function ($class) use ($dayName) {
            $schedule = $class->schedule;
            if (!$schedule || !isset($schedule['days'])) {
                return false;
            }
            return in_array($dayName, $schedule['days']);
        });

        $classesCompletedToday = 0;
        foreach ($todayClasses as $class) {
            $marked = StudentAttendance::where('class_room_id', $class->id)
                ->where('date', $todayDate)
                ->exists();
            if ($marked) {
                $classesCompletedToday++;
            }
        }

        $totalAttendance = StudentAttendance::whereIn('class_room_id', $classIds)->count();
        $presentCount = StudentAttendance::whereIn('class_room_id', $classIds)
            ->where('status', 'present')
            ->count();
        $rate = $totalAttendance > 0 ? round(($presentCount / $totalAttendance) * 100, 1) : 0;

        return response()->json([
            'total_classes' => $classes->count(),
            'total_students' => $studentIds->count(),
            'today_classes' => $todayClasses->count(),
            'attendance_rate' => $rate,
            'classes_completed_today' => $classesCompletedToday,
        ]);
    }

    public function report(Request $request)
    {
        $teacherId = auth()->user()->teacher?->id ?? auth()->id();
        $classIds = ClassRoom::where('teacher_id', $teacherId)->pluck('id');

        $startDate = $request->start_date ?? Carbon::now()->subMonth()->toDateString();
        $endDate = $request->end_date ?? Carbon::now()->toDateString();

        $query = StudentAttendance::whereIn('class_room_id', $classIds)
            ->whereBetween('date', [$startDate, $endDate]);

        $totalClasses = $query->distinct('date')->count('date');
        $totalMarked = (clone $query)->count();
        $present = (clone $query)->where('status', 'present')->count();
        $rate = $totalMarked > 0 ? round(($present / $totalMarked) * 100, 1) : 0;

        $studentIds = DB::table('class_room_students')
            ->whereIn('class_room_id', $classIds)
            ->pluck('student_id')
            ->unique()
            ->count();

        return response()->json([
            'period' => "{$startDate} to {$endDate}",
            'classes_conducted' => $totalClasses,
            'attendance_marked' => $totalMarked,
            'average_attendance_rate' => $rate,
            'students_tracked' => $studentIds,
        ]);
    }

    public function schedule(Request $request)
    {
        $teacherId = auth()->user()->teacher?->id ?? auth()->id();
        $date = $request->date ? Carbon::parse($request->date) : Carbon::now();
        $dayName = $date->format('D');

        $classes = ClassRoom::where('teacher_id', $teacherId)->get();

        $schedule = $classes->filter(function ($class) use ($dayName) {
            $schedule = $class->schedule;
            if (!$schedule || !isset($schedule['days'])) {
                return false;
            }
            return in_array($dayName, $schedule['days']);
        })->map(function ($class) use ($date) {
            $attendanceMarked = StudentAttendance::where('class_room_id', $class->id)
                ->where('date', $date->toDateString())
                ->exists();

            $now = Carbon::now();
            $startTime = $class->schedule['start_time'] ?? '00:00';
            $endTime = $class->schedule['end_time'] ?? '23:59';

            $status = 'upcoming';
            if ($attendanceMarked) {
                $status = 'completed';
            } elseif ($now->format('H:i') >= $startTime && $now->format('H:i') <= $endTime) {
                $status = 'ongoing';
            }

            return [
                'class_id' => $class->id,
                'class_name' => $class->name,
                'subject' => $class->subject,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'room' => $class->room,
                'status' => $status,
            ];
        })->values();

        return response()->json($schedule);
    }
}
