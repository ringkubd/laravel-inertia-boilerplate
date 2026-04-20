<?php

namespace App\Services;

use App\Models\Student;
use App\Models\Trade;
use Illuminate\Support\Collection;

class MadrasahNotesheetDataService
{
    public function generate(string $academicSession, string $noteType, ?int $feePerStudent = null, ?int $centerFee = null, ?int $attachmentFee = null): array
    {
        $feeStructure = $this->feeStructure($noteType, $feePerStudent, $centerFee, $attachmentFee);

        $students = Student::query()
            ->select(['id', 'madrasha_id', 'madrasa_trade_id', 'ssc_session'])
            ->whereNotNull('madrasha_id')
            ->where('status', 1)
            ->where('ssc_session', $academicSession)
            ->with(['madrasha:id,name'])
            ->get();

        $tradeMap = Trade::query()
            ->where('is_madrasa', 1)
            ->pluck('name', 'id');

        $grouped = $students
            ->groupBy(fn($student) => $student->madrasha?->name ?? 'Unknown Madrasah')
            ->map(function (Collection $madrasahStudents, string $madrasahName) use ($feeStructure, $tradeMap) {
                $rows = $madrasahStudents
                    ->groupBy('madrasa_trade_id')
                    ->map(function (Collection $tradeStudents, $tradeId) use ($feeStructure, $tradeMap, $madrasahName) {
                        $count = $tradeStudents->count();
                        $tradeName = $tradeMap[$tradeId] ?? ($tradeId ?: 'Unknown Trade');

                        return $this->buildRow($madrasahName, $tradeName, $count, $feeStructure);
                    })
                    ->sortBy('trade_name')
                    ->values();

                return [
                    'madrasah_name' => $madrasahName,
                    'rowspan' => $rows->count(),
                    'rows' => $rows->all(),
                    'totals' => $this->sumRows($rows),
                ];
            })
            ->sortKeys()
            ->values();

        $flatRows = $grouped->flatMap(fn($group) => $group['rows'])->values();

        return [
            'note_type' => $noteType,
            'academic_session' => $academicSession,
            'fee_structure' => $feeStructure,
            'madrasah_groups' => $grouped->all(),
            'rows' => $flatRows->all(),
            'totals' => $this->sumRows($flatRows),
            'total_students' => $flatRows->sum('student_count'),
        ];
    }

    private function buildRow(string $madrasahName, string $tradeName, int $count, array $feeStructure): array
    {
        $bteb = $feeStructure['bteb'];
        $center = $feeStructure['center'];
        $attachment = $feeStructure['attachment'];
        $perStudentTotal = $bteb + $center + $attachment;

        return [
            'madrasah_name' => $madrasahName,
            'trade_name' => $tradeName,
            'student_count' => $count,
            'per_student' => [
                'bteb' => $bteb,
                'center' => $center,
                'attachment' => $attachment,
                'total' => $perStudentTotal,
            ],
            'totals' => [
                'bteb' => $count * $bteb,
                'center' => $count * $center,
                'attachment' => $count * $attachment,
                'total' => $count * $perStudentTotal,
            ],
        ];
    }

    private function sumRows(Collection $rows): array
    {
        return [
            'student_count' => $rows->sum('student_count'),
            'bteb' => $rows->sum('totals.bteb'),
            'center' => $rows->sum('totals.center'),
            'attachment' => $rows->sum('totals.attachment'),
            'total' => $rows->sum('totals.total'),
        ];
    }

    private function feeStructure(string $noteType, ?int $feePerStudent = null, ?int $centerFee = null, ?int $attachmentFee = null): array
    {
        return match ($noteType) {
            'class_9_registration' => [
                'label' => 'Class IX Registration',
                'bteb' => $feePerStudent ?? 240,
                'center' => 0,
                'attachment' => 0,
            ],
            'class_9_exam' => [
                'label' => 'Class IX Board Exam',
                'bteb' => $feePerStudent ?? 960,
                'center' => $centerFee ?? 945,
                'attachment' => $attachmentFee ?? 70,
            ],
            'class_10_exam' => [
                'label' => 'Class X Board Exam',
                'bteb' => $feePerStudent ?? 1080,
                'center' => $centerFee ?? 910,
                'attachment' => $attachmentFee ?? 70,
            ],
            'teacher_salary' => [
                'label' => 'Teacher Salary',
                'bteb' => 0,
                'center' => 0,
                'attachment' => 0,
            ],
            default => [
                'label' => 'Madrasah Notesheet',
                'bteb' => 0,
                'center' => 0,
                'attachment' => 0,
            ],
        };
    }
}
