<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Models\Invoice;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class FinancialReportController extends Controller
{
    public function index(Request $request)
    {
        $invoices = Invoice::with('student.results')
            ->whereHas('student', fn($q) => $q->whereNotNull('polytechnic_id'))
            ->when($request->session, fn($q, $v) => $q->where('session', $v))
            ->when($request->from_date, fn($q, $v) => $q->whereDate('invoice_date', '>=', $v))
            ->when($request->to_date, fn($q, $v) => $q->whereDate('invoice_date', '<=', $v))
            ->get();

        $totalPaid = $invoices->sum('amount');
        $totalStudentsPaid = $invoices->pluck('student_id')->unique()->count();

        $monthly = $invoices->groupBy(fn($inv) => Carbon::parse($inv->invoice_month)->format('Y-m'))
            ->sortKeys()
            ->map(function ($invs, $month) {
                $studentIds = $invs->pluck('student_id')->unique();
                $sum = $invs->sum('amount');
                $count = $studentIds->count();
                return [
                    'month' => $month,
                    'total_amount' => round($sum, 2),
                    'students_paid' => $count,
                    'avg_per_student' => $count > 0 ? round($sum / $count, 2) : 0,
                ];
            })->values();

        $completedStudentIds = Student::whereHas('results', fn($q) =>
            $q->where('semester', 8)->where('status', 'Passed')
        )->whereNotNull('polytechnic_id')->pluck('id');

        $completedInvs = $invoices->whereIn('student_id', $completedStudentIds);

        $completedByStudent = $completedInvs->groupBy('student_id')->map(function ($invs) {
            $student = $invs->first()->student;
            $total = $invs->sum('amount');
            $monthsActive = $invs->groupBy(fn($inv) => Carbon::parse($inv->invoice_month)->format('Y-m'))->count();
            return [
                'student_id' => $student->id,
                'name' => $student->name,
                'polytechnic_session' => $student->polytechnic_session,
                'total_received' => round($total, 2),
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

        $bySession = $invoices->groupBy('session')
            ->sortKeysDesc()
            ->map(function ($invs, $session) {
                $studentIds = $invs->pluck('student_id')->unique();
                $sum = $invs->sum('amount');
                $count = $studentIds->count();
                return [
                    'session' => $session,
                    'total_amount' => round($sum, 2),
                    'students' => $count,
                    'avg_per_student' => $count > 0 ? round($sum / $count, 2) : 0,
                ];
            })->values();

        $bySessionSemester = $invoices->groupBy(fn($inv) => $inv->session . '|' . $inv->semester)
            ->sortKeys()
            ->map(function ($invs, $key) {
                [$session, $semester] = explode('|', $key);
                $studentIds = $invs->pluck('student_id')->unique();
                return [
                    'session' => $session,
                    'semester' => (int) $semester,
                    'total_amount' => round($invs->sum('amount'), 2),
                    'students' => $studentIds->count(),
                ];
            })->values();

        return Inertia::render('FinancialReport/Index', [
            'summary' => [
                'total_paid' => round($totalPaid, 2),
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
