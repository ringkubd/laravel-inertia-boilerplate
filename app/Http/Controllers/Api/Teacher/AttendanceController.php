<?php

namespace App\Http\Controllers\Api\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\StudentAttendance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AttendanceController extends Controller
{
    use \App\Http\Controllers\Api\Teacher\Concerns\AuthorizesTeachers;
    public function byClass(Request $request, $classId)
    {
        $class = $this->findOwnedClassOrFail($classId);
        $date = $request->date ?? Carbon::now()->toDateString();

        $records = StudentAttendance::where('class_room_id', $classId)
            ->where('date', $date)
            ->with('student')
            ->get();

        return response()->json(
            $records->map(function ($record) {
                return [
                    'id' => $record->id,
                    'student_id' => $record->student_id,
                    'class_id' => $record->class_room_id,
                    'date' => $record->date->toDateString(),
                    'status' => $record->status,
                    'marked_by' => $record->marked_by,
                    'location' => $record->location,
                    'created_at' => $record->created_at,
                    'updated_at' => $record->updated_at,
                ];
            })
        );
    }

    public function mark(Request $request)
    {
        $this->findOwnedClassOrFail($request->class_id);

        $validator = Validator::make($request->all(), [
            'class_id' => 'required|exists:class_rooms,id',
            'date' => 'required|date',
            'students' => 'required|array|min:1',
            'students.*.student_id' => 'required|exists:students,id',
            'students.*.status' => 'required|in:present,absent,late',
            'location' => 'nullable|array',
            'location.latitude' => 'nullable|numeric',
            'location.longitude' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $records = [];
        $userId = auth()->id();

        foreach ($data['students'] as $student) {
            $record = StudentAttendance::updateOrCreate(
                [
                    'student_id' => $student['student_id'],
                    'class_room_id' => $data['class_id'],
                    'date' => $data['date'],
                ],
                [
                    'status' => $student['status'],
                    'marked_by' => $userId,
                    'location' => isset($data['location']) ? json_encode($data['location']) : null,
                ]
            );
            $records[] = $record;
        }

        return response()->json([
            'message' => 'Attendance marked successfully',
            'records' => $records,
        ]);
    }

    public function stats(Request $request, $classId)
    {
        $class = $this->findOwnedClassOrFail($classId);

        $query = StudentAttendance::where('class_room_id', $classId);

        if ($request->start_date) {
            $query->where('date', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->where('date', '<=', $request->end_date);
        }

        $totalStudents = DB::table('class_room_students')
            ->where('class_room_id', $classId)
            ->count();

        $present = (clone $query)->where('status', 'present')->count();
        $absent = (clone $query)->where('status', 'absent')->count();
        $late = (clone $query)->where('status', 'late')->count();
        $totalMarked = $present + $absent + $late;
        $rate = $totalMarked > 0 ? round(($present / $totalMarked) * 100, 1) : 0;

        return response()->json([
            'total_students' => $totalStudents,
            'present' => $present,
            'absent' => $absent,
            'late' => $late,
            'attendance_rate' => $rate,
        ]);
    }

    public function history(Request $request)
    {
        $query = StudentAttendance::where('marked_by', auth()->id())
            ->with(['student', 'classRoom']);

        if ($request->start_date) {
            $query->where('date', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->where('date', '<=', $request->end_date);
        }

        $records = $query->latest('date')->get();

        return response()->json(
            $records->map(function ($record) {
                return [
                    'id' => $record->id,
                    'student_name' => $record->student?->name,
                    'class_name' => $record->classRoom?->name,
                    'date' => $record->date->toDateString(),
                    'status' => $record->status,
                    'created_at' => $record->created_at,
                ];
            })
        );
    }

    public function verifyLocation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $madrasah = auth()->user()->madrasah;
        if (!$madrasah || !$madrasah->location) {
            return response()->json([
                'verified' => false,
                'message' => 'Madrasah location not configured',
            ]);
        }

        $madrasahLocation = json_decode($madrasah->location);
        if (!$madrasahLocation) {
            return response()->json([
                'verified' => false,
                'message' => 'Madrasah location not configured',
            ]);
        }

        $distance = $this->haversineDistance(
            $request->latitude,
            $request->longitude,
            (float) ($madrasahLocation->latitude ?? $madrasahLocation->lat ?? 0),
            (float) ($madrasahLocation->longitude ?? $madrasahLocation->lng ?? 0)
        );

        $radiusMeters = 100;
        $verified = $distance <= $radiusMeters;

        return response()->json([
            'verified' => $verified,
            'message' => $verified
                ? 'Location verified. You are within Madrasah premises.'
                : 'You are outside the Madrasah premises. Distance: ' . round($distance) . 'm',
        ]);
    }

    private function haversineDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }
}
