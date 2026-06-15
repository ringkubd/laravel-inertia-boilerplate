<?php

namespace App\Http\Controllers\Madrasa;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\Madrasha;
use App\Models\Student;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::query()
            ->with('madrasha', 'classroom', 'madrasahResult')
            ->whereNull('polytechnic_id');

        if ($request->session) {
            $query->where('current_session', $request->session);
        }
        if ($request->madrasah) {
            $query->where('madrasha_id', $request->madrasah);
        }
        if ($request->trade) {
            $query->where('madrasa_trade_id', $request->trade);
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
        ];

        $bySession = $data->groupBy(function ($d) {
            return $d['student']->current_session ?? 'Unknown';
        })->map(function ($group) {
            return [
                'total' => $group->count(),
                'continuing' => $group->where('status', 'continuing')->count(),
                'completed' => $group->where('status', 'completed')->count(),
                'dropout' => $group->where('status', 'dropout')->count(),
            ];
        });

        $byMadrasah = $data->groupBy(function ($d) {
            return $d['student']->madrasha?->name ?? 'Unknown';
        })->map(function ($group) {
            return [
                'total' => $group->count(),
                'continuing' => $group->where('status', 'continuing')->count(),
                'completed' => $group->where('status', 'completed')->count(),
                'dropout' => $group->where('status', 'dropout')->count(),
            ];
        });

        $byClass = $data->groupBy(function ($d) {
            $class = $d['student']->classroom->first();
            return $class?->name ?? 'N/A';
        })->map(function ($group) {
            return [
                'total' => $group->count(),
                'continuing' => $group->where('status', 'continuing')->count(),
                'completed' => $group->where('status', 'completed')->count(),
                'dropout' => $group->where('status', 'dropout')->count(),
            ];
        });

        $byTrade = $data->groupBy(function ($d) {
            return $d['student']->madrasa_trade_id ?? 'N/A';
        })->filter(function ($g, $key) {
            return $key !== 'N/A';
        })->map(function ($group) {
            return [
                'total' => $group->count(),
                'continuing' => $group->where('status', 'continuing')->count(),
                'completed' => $group->where('status', 'completed')->count(),
                'dropout' => $group->where('status', 'dropout')->count(),
            ];
        });

        return Inertia::render('Madrasa/Report/Index', [
            'summary' => $summary,
            'bySession' => $bySession,
            'byMadrasah' => $byMadrasah,
            'byClass' => $byClass,
            'byTrade' => $byTrade,
            'filters' => $request->only(['session', 'madrasah', 'trade']),
            'sessions' => AcademicSession::all(['session'])->pluck('session'),
            'madrasahs' => Madrasha::select('id', 'name')->get(),
            'trades' => $students->pluck('madrasa_trade_id')->unique()->filter()->values(),
        ]);
    }

    private function resolveStatus($student)
    {
        $result = $student->madrasahResult;
        if ($result && $result->status === 'Pass') {
            return 'completed';
        }
        $st = $student->status;
        if ($st === 0) {
            return 'dropout';
        }
        return 'continuing';
    }
}
