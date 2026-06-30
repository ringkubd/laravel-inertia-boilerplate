<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Models\PaymentSlip;
use App\Models\Student;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FinancialReportController extends Controller
{
    public function index(Request $request)
    {
        $paymentSlips = PaymentSlip::with('student.results')
            ->where('status', 1)
            ->whereHas('student', fn($q) => $q->whereNotNull('polytechnic_id'))
            ->when($request->session, fn($q, $v) => $q->whereHas('student', fn($q) => $q->where('polytechnic_session', $v)))
            ->when($request->from_date, fn($q, $v) => $q->whereDate('created_at', '>=', $v))
            ->when($request->to_date, fn($q, $v) => $q->whereDate('created_at', '<=', $v))
            ->get();

        $totalPaid = $paymentSlips->sum('amount');
        $totalStudentsPaid = $paymentSlips->pluck('student_id')->unique()->count();

        $monthly = $paymentSlips->groupBy(fn($s) => $s->created_at->format('Y-m'))
            ->sortKeys()
            ->map(function ($slips, $month) {
                $studentIds = $slips->pluck('student_id')->unique();
                $sum = $slips->sum('amount');
                $count = $studentIds->count();
                return [
                    'month' => $month,
                    'total_amount' => $sum,
                    'students_paid' => $count,
                    'avg_per_student' => $count > 0 ? round($sum / $count, 2) : 0,
                ];
            })->values();

        $completedStudentIds = Student::whereHas('results', fn($q) =>
            $q->where('semester', 8)->where('status', 'Passed')
        )->whereNotNull('polytechnic_id')->pluck('id');

        $completedSlips = $paymentSlips->whereIn('student_id', $completedStudentIds);

        $completedByStudent = $completedSlips->groupBy('student_id')->map(function ($slips) {
            $student = $slips->first()->student;
            $total = $slips->sum('amount');
            $monthsActive = $slips->groupBy(fn($s) => $s->created_at->format('Y-m'))->count();
            return [
                'student_id' => $student->id,
                'name' => $student->name,
                'polytechnic_session' => $student->polytechnic_session,
                'total_received' => $total,
                'months_active' => $monthsActive,
                'avg_monthly' => $monthsActive > 0 ? round($total / $monthsActive, 2) : 0,
            ];
        })->values();

        $completedSummary = [
            'total_students' => $completedByStudent->count(),
            'total_received' => round($completedByStudent->sum('total_received'), 2),
            'avg_monthly_all' => $completedByStudent->count() > 0
                ? round($completedByStudent->avg('avg_monthly'), 2) : 0,
            'avg_total_per_student' => $completedByStudent->count() > 0
                ? round($completedByStudent->avg('total_received'), 2) : 0,
        ];

        $bySession = $paymentSlips->groupBy(fn($s) => $s->student?->polytechnic_session ?? 'Unknown')
            ->sortKeysDesc()
            ->map(function ($slips, $session) {
                $studentIds = $slips->pluck('student_id')->unique();
                $sum = $slips->sum('amount');
                $count = $studentIds->count();
                return [
                    'session' => $session,
                    'total_amount' => $sum,
                    'students' => $count,
                    'avg_per_student' => $count > 0 ? round($sum / $count, 2) : 0,
                ];
            })->values();

        $bySessionSemester = $paymentSlips->groupBy(fn($s) =>
            ($s->student?->polytechnic_session ?? 'Unknown') . '|' . $s->semester
        )->sortKeys()->map(function ($slips, $key) {
            [$session, $semester] = explode('|', $key);
            $studentIds = $slips->pluck('student_id')->unique();
            return [
                'session' => $session,
                'semester' => (int) $semester,
                'total_amount' => $slips->sum('amount'),
                'students' => $studentIds->count(),
            ];
        })->values();

        return Inertia::render('FinancialReport/Index', [
            'summary' => [
                'total_paid' => $totalPaid,
                'total_students_paid' => $totalStudentsPaid,
                'avg_per_student_overall' => $totalStudentsPaid > 0 ? round($totalPaid / $totalStudentsPaid, 2) : 0,
                'total_completed' => $completedSummary['total_students'],
                'completed_total_received' => $completedSummary['total_received'],
            ],
            'monthly' => $monthly,
            'completedByStudent' => $completedByStudent,
            'completedSummary' => $completedSummary,
            'bySession' => $bySession,
            'bySessionSemester' => $bySessionSemester,
            'filters' => $request->only(['session', 'from_date', 'to_date']),
            'sessions' => AcademicSession::all(['session'])->pluck('session'),
        ]);
    }
}
