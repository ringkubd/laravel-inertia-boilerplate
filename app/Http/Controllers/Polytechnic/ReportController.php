<?php

namespace App\Http\Controllers\Polytechnic;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\Polytechnic;
use App\Models\Student;
use App\Models\Trade;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::query()
            ->with('polytechnicInfo', 'classroom')
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

        $bySession = $students->groupBy('polytechnic_session')->map(function ($group) {
            return [
                'total' => $group->count(),
                'continuing' => $group->where('status', 1)->count(),
                'dropout' => $group->where('status', 0)->count(),
                'suspended' => $group->where('status', 2)->count(),
            ];
        })->filter(function ($item, $key) {
            return !empty($key);
        });

        $byPolytechnic = $students->groupBy(function ($s) {
            return $s->polytechnicInfo?->name ?? 'Unknown';
        })->map(function ($group) {
            return [
                'total' => $group->count(),
                'continuing' => $group->where('status', 1)->count(),
                'dropout' => $group->where('status', 0)->count(),
                'suspended' => $group->where('status', 2)->count(),
            ];
        });

        $bySemester = $students->groupBy('semester')->map(function ($group) {
            return [
                'total' => $group->count(),
                'continuing' => $group->where('status', 1)->count(),
                'dropout' => $group->where('status', 0)->count(),
                'suspended' => $group->where('status', 2)->count(),
            ];
        })->filter(function ($item, $key) {
            return $key !== null && $key !== '';
        })->sortKeys();

        $byTrade = $students->groupBy('polytechnic_trade_id')->map(function ($group) {
            return [
                'total' => $group->count(),
                'continuing' => $group->where('status', 1)->count(),
                'dropout' => $group->where('status', 0)->count(),
                'suspended' => $group->where('status', 2)->count(),
            ];
        })->filter(function ($item, $key) {
            return !empty($key);
        });

        return Inertia::render('Polytechnic/Report/Index', [
            'summary' => [
                'total' => $students->count(),
                'continuing' => $students->where('status', 1)->count(),
                'dropout' => $students->where('status', 0)->count(),
                'suspended' => $students->where('status', 2)->count(),
            ],
            'bySession' => $bySession,
            'byPolytechnic' => $byPolytechnic,
            'bySemester' => $bySemester,
            'byTrade' => $byTrade,
            'filters' => $request->only(['session', 'polytechnic', 'trade']),
            'sessions' => AcademicSession::all(['session'])->pluck('session'),
            'polytechnics' => Polytechnic::select('id', 'name')->get(),
            'trades' => Trade::select('name')->whereHas('students', function ($q) {
                $q->whereNotNull('polytechnic_id');
            })->get()->pluck('name'),
        ]);
    }
}
