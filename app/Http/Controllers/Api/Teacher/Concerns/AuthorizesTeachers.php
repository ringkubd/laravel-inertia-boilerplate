<?php

namespace App\Http\Controllers\Api\Teacher\Concerns;

use App\Models\ClassRoom;
use App\Models\Student;
use Illuminate\Database\Eloquent\Model;

trait AuthorizesTeachers
{
    protected function teacherId(): int
    {
        return auth()->user()->teacher?->id ?? auth()->id();
    }

    protected function ownsClass($class): bool
    {
        $id = is_object($class) ? $class->id : $class;
        return ClassRoom::where('id', $id)->where('teacher_id', $this->teacherId())->exists();
    }

    protected function findOwnedClassOrFail(int $id): ClassRoom
    {
        $class = ClassRoom::findOrFail($id);
        if ((int) $class->teacher_id !== $this->teacherId()) {
            abort(403, 'You do not own this class');
        }
        return $class;
    }

    protected function ownsStudent(int $studentId): bool
    {
        $classIds = ClassRoom::where('teacher_id', $this->teacherId())->pluck('id');
        return \DB::table('class_room_students')
            ->where('class_room_id', $classIds)
            ->where('student_id', $studentId)
            ->exists();
    }

    protected function findOwnedStudentOrFail(int $id): Student
    {
        $student = Student::findOrFail($id);
        if (!$this->ownsStudent($student->id)) {
            abort(403, 'This student is not in your classes');
        }
        return $student;
    }

    protected function teacherClassIds(): array
    {
        return ClassRoom::where('teacher_id', $this->teacherId())->pluck('id')->toArray();
    }

    protected function teacherStudentIds(): array
    {
        return \DB::table('class_room_students')
            ->whereIn('class_room_id', $this->teacherClassIds())
            ->pluck('student_id')
            ->unique()
            ->toArray();
    }
}
