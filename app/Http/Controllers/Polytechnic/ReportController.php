<?php

namespace App\Http\Controllers\Polytechnic;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\Polytechnic;
use App\Models\Student;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::query()
            ->with('polytechnicInfo', 'classroom', 'results')
            ->whereNotNull('polytechnic_id');

        if ($request->session) {
            $query->where('polytechnic_session', $request->session);
        }
        if ($request->polytechnic) {
            $query->where('polytechnic_id', $request->polytechnic);
        }
        if ($request->trade) {
            $query->where('polytechnic_trade_id', $request->trade);
        }

        $students = $query->get();

        $data = $students->map(function ($s) {
            return [
                'student' => $s,
                'status' => $this->resolveStatus($s),
            ];
        });

        $summary = [
            'total' => $data->count(),
            'continuing' => $data->where('status', 'continuing')->count(),
            'completed' => $data->where('status', 'completed')->count(),
            'dropout' => $data->where('status', 'dropout')->count(),
            'no_result' => $data->where('status', 'no_result')->count(),
        ];

        $bySession = $data->groupBy(function ($d) {
            return $d['student']->polytechnic_session ?? 'Unknown';
        })->filter(function ($g, $key) {
            return $key !== 'Unknown';
        })->map(function ($group) {
            return [
                'total' => $group->count(),
                'continuing' => $group->where('status', 'continuing')->count(),
                'completed' => $group->where('status', 'completed')->count(),
                'dropout' => $group->where('status', 'dropout')->count(),
                'no_result' => $group->where('status', 'no_result')->count(),
            ];
        })->sortKeysDesc();

        $byPolytechnic = $data->groupBy(function ($d) {
            return $d['student']->polytechnicInfo?->name ?? 'Unknown';
        })->map(function ($group) {
            return [
                'total' => $group->count(),
                'continuing' => $group->where('status', 'continuing')->count(),
                'completed' => $group->where('status', 'completed')->count(),
                'dropout' => $group->where('status', 'dropout')->count(),
                'no_result' => $group->where('status', 'no_result')->count(),
            ];
        });

        $bySemester = $data->groupBy(function ($d) {
            return $d['student']->semester ?? 'N/A';
        })->filter(function ($g, $key) {
            return $key !== 'N/A';
        })->sortKeys()->map(function ($group) {
            return [
                'total' => $group->count(),
                'continuing' => $group->where('status', 'continuing')->count(),
                'completed' => $group->where('status', 'completed')->count(),
                'dropout' => $group->where('status', 'dropout')->count(),
                'no_result' => $group->where('status', 'no_result')->count(),
            ];
        });

        $byTrade = $data->groupBy(function ($d) {
            return $d['student']->polytechnic_trade_id ?? 'N/A';
        })->filter(function ($g, $key) {
            return $key !== 'N/A';
        })->map(function ($group) {
            return [
                'total' => $group->count(),
                'continuing' => $group->where('status', 'continuing')->count(),
                'completed' => $group->where('status', 'completed')->count(),
                'dropout' => $group->where('status', 'dropout')->count(),
                'no_result' => $group->where('status', 'no_result')->count(),
            ];
        });

        return Inertia::render('Polytechnic/Report/Index', [
            'summary' => $summary,
            'bySession' => $bySession,
            'byPolytechnic' => $byPolytechnic,
            'bySemester' => $bySemester,
            'byTrade' => $byTrade,
            'filters' => $request->only(['session', 'polytechnic', 'trade']),
            'sessions' => AcademicSession::all(['session'])->pluck('session'),
            'polytechnics' => Polytechnic::select('id', 'name')->get(),
            'trades' => $students->pluck('polytechnic_trade_id')->unique()->filter()->values(),
        ]);
    }

    private function resolveStatus($student)
    {
        $results = $student->results;

        $dropoutResult = $results->firstWhere('status', 'Dropout');
        if ($dropoutResult) {
            return 'dropout';
        }

        $sem8Passed = $results->where('semester', 8)->firstWhere('status', 'Passed');
        if ($sem8Passed) {
            return 'completed';
        }

        $hasAnyResult = $results->isNotEmpty();
        if (!$hasAnyResult) {
            return 'no_result';
        }

        $st = $student->status;
        if ($st === 0) {
            return 'dropout';
        }
        return 'continuing';
    }
}
