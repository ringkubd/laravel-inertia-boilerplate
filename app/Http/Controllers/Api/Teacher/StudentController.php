<?php

namespace App\Http\Controllers\Api\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use App\Models\ClassRoom as ClassRoomModel;

class StudentController extends Controller
{
    use \App\Http\Controllers\Api\Teacher\Concerns\AuthorizesTeachers;
    public function index(Request $request)
    {
        $query = $this->applyCommonFilters(Student::query(), $request);

        $students = $query->with('madrasha')->get();

        return response()->json(
            $students->map(function ($student) {
                return $this->formatStudent($student);
            })
        );
    }

    public function teacherStudents(Request $request)
    {
        $teacherId = auth()->user()->teacher?->id ?? auth()->id();
        $classIds = ClassRoomModel::where('teacher_id', $teacherId)->pluck('id');

        $studentIds = DB::table('class_room_students')
            ->whereIn('class_room_id', $classIds)
            ->pluck('student_id')
            ->unique();

        $query = Student::query();

        if ($studentIds->isNotEmpty()) {
            $query->whereIn('id', $studentIds);
        } elseif (auth()->user()->madrasha_id) {
            // Fallback: show all students from same madrasah
            $query->where('madrasha_id', auth()->user()->madrasha_id);
        }

        $query = $this->applyCommonFilters($query, $request);
        $students = $query->get();

        return response()->json(
            $students->map(function ($student) {
                return $this->formatStudent($student);
            })
        );
    }

    private function applyCommonFilters($query, Request $request)
    {
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('class_roll', 'like', "%{$request->search}%")
                  ->orWhere('student_id', 'like', "%{$request->search}%");
            });
        }

        if ($request->performance) {
            $performanceMap = [
                'excellent' => [90, 100],
                'good' => [75, 89],
                'average' => [60, 74],
                'needs-attention' => [0, 59],
            ];

            if (isset($performanceMap[$request->performance])) {
                [$min, $max] = $performanceMap[$request->performance];
                $studentIds = StudentAttendance::select('student_id')
                    ->selectRaw('COUNT(*) as total')
                    ->selectRaw('SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as present_count')
                    ->groupBy('student_id')
                    ->havingRaw("(present_count * 100.0 / total) BETWEEN ? AND ?", [$min, $max])
                    ->pluck('student_id');
                $query->whereIn('id', $studentIds);
            }
        }

        if ($request->type === 'polytechnic') {
            $query->whereNotNull('polytechnic_id');
        } elseif ($request->type === 'madrasah') {
            $query->whereNull('polytechnic_id');
        }

        if ($request->session) {
            $query->where(function ($q) use ($request) {
                $q->where('ssc_session', $request->session)
                  ->orWhere('polytechnic_session', $request->session);
            });
        }

        return $query;
    }

    public function show($id)
    {
        $student = $this->findOwnedStudentOrFail($id);
        $student->load(['madrasha', 'classroom', 'results', 'madrasahResult']);
        return response()->json($this->formatStudentDetail($student));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'roll_number' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
            'address' => 'nullable|string',
            'guardian_name' => 'nullable|string|max:255',
            'guardian_phone' => 'nullable|string|max:255',
            'class_ids' => 'nullable|array',
            'class_ids.*' => 'exists:class_rooms,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        $student = Student::create([
            'name' => $data['name'],
            'class_roll' => $data['roll_number'] ?? null,
            'mobile' => $data['phone'] ?? null,
            'father_name' => $data['guardian_name'] ?? null,
            'guardian_mobile' => $data['guardian_phone'] ?? null,
            'present_address' => $data['address'] ?? null,
            'madrasha_id' => auth()->user()->madrasha_id,
        ]);

        if (!empty($data['email'])) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make('password123'),
                'madrasha_id' => auth()->user()->madrasha_id,
            ]);
            $user->assignRole(Role::where('name', 'Student')->first());
            $student->update(['users_id' => $user->id]);
        }

        if (!empty($data['class_ids'])) {
            $student->classroom()->attach($data['class_ids']);
        }

        return response()->json($this->formatStudent($student), 201);
    }

    public function update(Request $request, $id)
    {
        $student = $this->findOwnedStudentOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'roll_number' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
            'address' => 'nullable|string',
            'guardian_name' => 'nullable|string|max:255',
            'guardian_phone' => 'nullable|string|max:255',
            'class_ids' => 'nullable|array',
            'class_ids.*' => 'exists:class_rooms,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        $student->update([
            'name' => $data['name'] ?? $student->name,
            'class_roll' => $data['roll_number'] ?? $student->class_roll,
            'mobile' => $data['phone'] ?? $student->mobile,
            'father_name' => $data['guardian_name'] ?? $student->father_name,
            'guardian_mobile' => $data['guardian_phone'] ?? $student->guardian_mobile,
            'present_address' => $data['address'] ?? $student->present_address,
        ]);

        if (!empty($data['class_ids'])) {
            $student->classroom()->sync($data['class_ids']);
        }

        return response()->json($this->formatStudent($student));
    }

    public function destroy($id)
    {
        $student = $this->findOwnedStudentOrFail($id);
        $student->delete();
        return response()->json(['message' => 'Student deleted successfully']);
    }

    public function performance(Request $request, $id)
    {
        $student = $this->findOwnedStudentOrFail($id);

        $query = StudentAttendance::where('student_id', $id);

        if ($request->start_date) {
            $query->where('date', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->where('date', '<=', $request->end_date);
        }

        $total = $query->count();
        $present = (clone $query)->where('status', 'present')->count();
        $absent = (clone $query)->where('status', 'absent')->count();
        $late = (clone $query)->where('status', 'late')->count();
        $rate = $total > 0 ? round(($present / $total) * 100, 1) : 0;

        $grade = 'average';
        if ($rate >= 90) $grade = 'excellent';
        elseif ($rate >= 75) $grade = 'good';
        elseif ($rate >= 60) $grade = 'average';
        else $grade = 'needs-attention';

        return response()->json([
            'student_id' => (int) $id,
            'attendance_rate' => $rate,
            'total_classes' => $total,
            'present' => $present,
            'absent' => $absent,
            'late' => $late,
            'performance_grade' => $grade,
        ]);
    }

    public function enroll(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'class_id' => 'required|exists:class_rooms,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $class = ClassRoomModel::findOrFail($request->class_id);
        if ((int) $class->teacher_id !== $this->teacherId()) {
            return response()->json(['message' => 'You do not own this class'], 403);
        }

        $student = $this->findOwnedStudentOrFail($id);
        $student->classroom()->syncWithoutDetaching([$request->class_id]);

        return response()->json(['message' => 'Student enrolled successfully']);
    }

    private function formatStudent($student)
    {
        $attendance = StudentAttendance::where('student_id', $student->id);
        $total = (clone $attendance)->count();
        $present = (clone $attendance)->where('status', 'present')->count();
        $rate = $total > 0 ? round(($present / $total) * 100, 1) : 0;

        $grade = 'average';
        if ($rate >= 90) $grade = 'excellent';
        elseif ($rate >= 75) $grade = 'good';
        elseif ($rate >= 60) $grade = 'average';
        else $grade = 'needs-attention';

        $last = (clone $attendance)->latest('date')->first();

        return [
            'id' => $student->id,
            'name' => $student->name,
            'roll_number' => $student->class_roll ?? $student->student_id,
            'email' => $student->user?->email,
            'phone' => $student->mobile,
            'date_of_birth' => $student->dob ? \Carbon\Carbon::parse($student->dob)->toDateString() : null,
            'address' => $student->present_address,
            'guardian_name' => $student->father_name ?? $student->guardian_name,
            'guardian_phone' => $student->guardian_mobile,
            'classes' => $student->classroom->pluck('id')->toArray(),
            'attendance_rate' => $rate,
            'performance' => $grade,
            'last_attendance' => $last?->date ? \Carbon\Carbon::parse($last->date)->toDateString() : null,
            'type' => $student->polytechnic_id ? 'polytechnic' : 'madrasah',
            'ssc_session' => $student->ssc_session,
            'polytechnic_session' => $student->polytechnic_session,
            'madrasa_completed' => (bool) $student->madrasa_completed,
            'polytechnic_completed' => (bool) $student->polytechnic_completed,
        ];
    }

    private function formatStudentDetail($student)
    {
        $data = $this->formatStudent($student);
        $attendance = StudentAttendance::where('student_id', $student->id);

        $data['present_count'] = (clone $attendance)->where('status', 'present')->count();
        $data['absent_count'] = (clone $attendance)->where('status', 'absent')->count();
        $data['late_count'] = (clone $attendance)->where('status', 'late')->count();
        $data['total_classes_count'] = (clone $attendance)->count();
        $data['grade'] = $data['performance'];

        return $data;
    }
}
