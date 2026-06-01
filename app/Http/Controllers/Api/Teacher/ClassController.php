<?php

namespace App\Http\Controllers\Api\Teacher;

use App\Http\Controllers\Controller;

use App\Models\ClassRoom;
use App\Models\StudentAttendance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ClassController extends Controller
{
    use \App\Http\Controllers\Api\Teacher\Concerns\AuthorizesTeachers;
    public function index()
    {
        $classes = ClassRoom::where('teacher_id', $this->teacherId())
            ->orderBy('name')
            ->get();

        return response()->json($this->formatClasses($classes));
    }

    public function today()
    {
        $dayName = Carbon::now()->format('D');
        $classes = ClassRoom::where('teacher_id', $this->teacherId())
            ->get()
            ->filter(function ($class) use ($dayName) {
                $schedule = $class->schedule;
                if (!$schedule || !isset($schedule['days'])) {
                    return false;
                }
                return in_array($dayName, $schedule['days']);
            })
            ->values();

        return response()->json($this->formatClasses($classes));
    }

    public function show($id)
    {
        $class = $this->findOwnedClassOrFail($id);
        return response()->json($this->formatClass($class));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'subject' => 'nullable|string|max:255',
            'section' => 'nullable|string|max:255',
            'room' => 'nullable|string|max:255',
            'schedule' => 'nullable|array',
            'schedule.days' => 'nullable|array',
            'schedule.days.*' => 'string',
            'schedule.start_time' => 'nullable|string',
            'schedule.end_time' => 'nullable|string',
            'class_name_number' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $class = ClassRoom::create(array_merge(
            $validator->validated(),
            ['teacher_id' => $this->teacherId()]
        ));

        return response()->json($this->formatClass($class), 201);
    }

    public function update(Request $request, $id)
    {
        $class = $this->findOwnedClassOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'subject' => 'nullable|string|max:255',
            'section' => 'nullable|string|max:255',
            'room' => 'nullable|string|max:255',
            'schedule' => 'nullable|array',
            'schedule.days' => 'nullable|array',
            'schedule.days.*' => 'string',
            'schedule.start_time' => 'nullable|string',
            'schedule.end_time' => 'nullable|string',
            'class_name_number' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $class->update($validator->validated());
        return response()->json($this->formatClass($class));
    }

    public function destroy($id)
    {
        $class = $this->findOwnedClassOrFail($id);
        $class->delete();
        return response()->json(['message' => 'Class deleted successfully']);
    }

    public function students($id)
    {
        $class = ClassRoom::with('students')->findOrFail($id);
        if ((int) $class->teacher_id !== $this->teacherId()) {
            abort(403, 'You do not own this class');
        }
        return response()->json(
            $class->students->map(function ($student) {
                return [
                    'id' => $student->id,
                    'name' => $student->name,
                    'roll_number' => $student->class_roll ?? $student->student_id,
                    'email' => $student->user?->email,
                    'phone' => $student->mobile,
                    'guardian_name' => $student->guardian_name ?? $student->father_name,
                    'guardian_phone' => $student->guardian_mobile,
                    'performance' => $this->getStudentPerformanceGrade($student->id),
                    'last_attendance' => $this->getLastAttendanceDate($student->id),
                ];
            })
        );
    }

    private function formatClasses($classes)
    {
        $classIds = $classes->pluck('id');
        $counts = DB::table('class_room_students')
            ->whereIn('class_room_id', $classIds)
            ->groupBy('class_room_id')
            ->selectRaw('class_room_id, COUNT(*) as count')
            ->pluck('count', 'class_room_id');

        return $classes->map(function ($class) use ($counts) {
            return $this->formatClass($class, $counts[$class->id] ?? 0);
        });
    }

    private function formatClass($class, int $studentCount = 0)
    {
        if (!$studentCount) {
            $studentCount = DB::table('class_room_students')
                ->where('class_room_id', $class->id)
                ->count();
        }

        $schedule = $class->schedule;
        if (is_string($schedule)) {
            $schedule = json_decode($schedule, true);
        }

        return [
            'id' => $class->id,
            'name' => $class->name,
            'subject' => $class->subject,
            'teacher_id' => (int) ($class->teacher_id ?? auth()->id()),
            'room' => $class->room,
            'schedule' => $schedule ?: [
                'days' => [],
                'start_time' => '08:00',
                'end_time' => '09:30',
            ],
            'students_count' => (int) $studentCount,
            'status' => $class->status ?? 'active',
            'created_at' => $class->created_at,
            'updated_at' => $class->updated_at,
        ];
    }

    private function getStudentPerformanceGrade($studentId)
    {
        $rate = StudentAttendance::where('student_id', $studentId)
            ->where('status', 'present')
            ->count();
        $total = StudentAttendance::where('student_id', $studentId)->count();

        if ($total === 0) return 'average';
        $percentage = ($rate / $total) * 100;

        if ($percentage >= 90) return 'excellent';
        if ($percentage >= 75) return 'good';
        if ($percentage >= 60) return 'average';
        return 'needs-attention';
    }

    private function getLastAttendanceDate($studentId)
    {
        $last = StudentAttendance::where('student_id', $studentId)
            ->latest('date')
            ->first();
        return $last ? $last->date->toDateString() : null;
    }
}
