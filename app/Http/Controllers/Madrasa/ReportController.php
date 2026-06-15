<?php

namespace App\Http\Controllers\Madrasa;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\ClassRoom;
use App\Models\Madrasha;
use App\Models\Student;
use App\Models\Trade;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::query()
            ->with('madrasha', 'classroom')
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

        $bySession = $students->groupBy('current_session')->map(function ($group) {
            return [
                'total' => $group->count(),
                'continuing' => $group->where('status', 1)->count(),
                'dropout' => $group->where('status', 0)->count(),
                'suspended' => $group->where('status', 2)->count(),
            ];
        });

        $byMadrasah = $students->groupBy(function ($s) {
            return $s->madrasha?->name ?? 'Unknown';
        })->map(function ($group) {
            return [
                'total' => $group->count(),
                'continuing' => $group->where('status', 1)->count(),
                'dropout' => $group->where('status', 0)->count(),
                'suspended' => $group->where('status', 2)->count(),
            ];
        });

        $byClass = $students->flatMap->classroom->groupBy('name')->map(function ($group) {
            return [
                'total' => $group->count(),
                'continuing' => $group->where('status', 1)->count(),
                'dropout' => $group->where('status', 0)->count(),
                'suspended' => $group->where('status', 2)->count(),
            ];
        });

        $byTrade = $students->groupBy('madrasa_trade_id')->map(function ($group) {
            return [
                'total' => $group->count(),
                'continuing' => $group->where('status', 1)->count(),
                'dropout' => $group->where('status', 0)->count(),
                'suspended' => $group->where('status', 2)->count(),
            ];
        })->filter(function ($item, $key) {
            return !empty($key);
        });

        return Inertia::render('Madrasa/Report/Index', [
            'summary' => [
                'total' => $students->count(),
                'continuing' => $students->where('status', 1)->count(),
                'dropout' => $students->where('status', 0)->count(),
                'suspended' => $students->where('status', 2)->count(),
            ],
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
}
