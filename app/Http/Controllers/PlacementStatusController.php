<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Models\Employment;
use App\Models\FurtherEducation;
use App\Models\OtherPlacementStatus;
use App\Models\Placement;
use App\Models\Madrasha;
use App\Models\Polytechnic;
use App\Models\Student;
use App\Models\MadrasahResult;
use App\Models\Result;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class PlacementStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $placements = Placement::query()
            ->with('status', 'student', 'student.polytechnicInfo')
            ->whereHas('student', function ($q) use ($request){
                $user_madrasah = auth()->user()?->madrasha_id;
                if ($user_madrasah){
                    $q->where('madrasha_id', $user_madrasah);
                }
                $q->when($request->search, function ($q, $v){
                    $q->where('name', 'like', "%$v%");
                });
            })
            ->latest()
            ->paginate(20);
        return Inertia::render('Placement/Index', [
            'placements' => $placements,
            'can' => [
                'create' => true,
                'delete' => true
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(Request $request): Response
    {
        $academic_sessions = AcademicSession::query()->pluck('session')->toArray();

        return Inertia::render('Placement/Create', [
            'academic_sessions' => $academic_sessions,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'final_result' => 'required',
            'present_status' => ['required',Rule::in(["higher_study", "employment", "other"])],
            'higher_study.institute_name' => 'required_if:present_status,higher_study',
            'higher_study.degree' => 'required_if:present_status,higher_study',
            'other.note' => 'required_if:present_status,other',
            'employment.organization' => 'required_if:present_status,employment',
            'employment.salary' => 'required_if:present_status,employment',
            'employment.position' => 'required_if:present_status,employment',
        ], [
            'higher_study.institute_name.required_if' => 'Institute name is required.',
            'higher_study.degree.required_if' => 'Degree is required.',
            'employment.organization.required_if' => 'Organization name is required.',
            'employment.salary.required_if' => 'Salary is required.',
            'employment.position.required_if' => 'Position is required.',
            'other.note.required_if' => 'Note is required.',
        ]);

        switch ($request->present_status){
            case "higher_study":
                $education = FurtherEducation::create([
                    'student_id' => $request->student_id,
                    'added_by' => $request->user()->id,
                    ...$request->higher_study
                ]);
                $education->placement()->create([
                    'student_id' => $request->student_id,
                    'final_result' => $request->final_result,
                    'added_by' => $request->user()->id,
                ]);
                break;
            case "employment":
                $employment = Employment::create([
                    'student_id' => $request->student_id,
                    'added_by' => $request->user()->id,
                    ...$request->employment
                ]);
                $employment->placement()->create([
                    'student_id' => $request->student_id,
                    'final_result' => $request->final_result,
                    'added_by' => $request->user()->id,
                ]);
                break;
            case "other":
                $employment = OtherPlacementStatus::create([
                    'student_id' => $request->student_id,
                    'added_by' => $request->user()->id,
                    ...$request->other
                ]);
                $employment->placement()->create([
                    'student_id' => $request->student_id,
                    'final_result' => $request->final_result,
                    'added_by' => $request->user()->id,
                ]);
                break;
        }
        return redirect()->route('placement.index');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return RedirectResponse
     */
    public function destroy($id): RedirectResponse
    {
        Placement::find($id)->delete();
        return redirect()->route('placement.index')->withFlash('success', 'Successfully deleted.');
    }

    /**
     * Show placement report with filtering and analytics
     *
     * @param Request $request
     * @return Response
     */
    public function report(Request $request): Response
    {
        $query = Placement::query()
            ->with('status', 'student', 'student.madrasha', 'student.polytechnicInfo');

        // Filter by madrasah
        if ($request->filled('madrasah_id')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('madrasha_id', $request->madrasah_id);
            });
        } else {
            // Apply default madrasah filter if user has one assigned
            $user_madrasah = auth()->user()?->madrasha_id;
            if ($user_madrasah) {
                $query->whereHas('student', function ($q) use ($user_madrasah) {
                    $q->where('madrasha_id', $user_madrasah);
                });
            }
        }

        // Filter by polytechnic
        if ($request->filled('polytechnic_id')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('polytechnic_id', $request->polytechnic_id);
            });
        }

        // Filter by session
        if ($request->filled('session')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('polytechnic_session', $request->session);
            });
        }

        $placements = $query->latest()->get();

        // Aggregate data for charts
        $reportData = $this->aggregatePlacementData($placements);

        // Additional session-level statistics (respect filter if provided)
        $sessionStats = $this->computeSessionStatistics($request->session);

        // Get filter options
        $sessions = AcademicSession::query()->pluck('session')->toArray();
        $madrasahs = Madrasha::query()->pluck('name', 'id')->toArray();
        $polytechnics = Polytechnic::query()->pluck('name', 'id')->toArray();


        return Inertia::render('Placement/Report', [
            'placements' => $placements,
            'reportData' => $reportData,
            'sessionStats' => $sessionStats,
            'sessions' => $sessions,
            'madrasahs' => $madrasahs,
            'polytechnics' => $polytechnics,
            'filters' => [
                'madrasah_id' => $request->madrasah_id,
                'polytechnic_id' => $request->polytechnic_id,
                'session' => $request->session,
            ]
        ]);
    }

    /**
     * Aggregate placement data for analytics
     *
     * @param \Illuminate\Database\Eloquent\Collection $placements
     * @return array
     */
    private function aggregatePlacementData($placements): array
    {
        $statusCounts = [
            'employment' => 0,
            'higher_study' => 0,
            'other' => 0,
        ];

        $byMadrasah = [];
        $byPolytechnic = [];
        $bySession = [];

        foreach ($placements as $placement) {
            // Count by status
            $statusType = $placement->present_status_type;
            if (strpos($statusType, 'Employment') !== false) {
                $statusCounts['employment']++;
            } elseif (strpos($statusType, 'FurtherEducation') !== false) {
                $statusCounts['higher_study']++;
            } else {
                $statusCounts['other']++;
            }

            // Count by madrasah
            $madrasahName = $placement->student?->madrasha?->name ?? 'Unknown';
            $byMadrasah[$madrasahName] = ($byMadrasah[$madrasahName] ?? 0) + 1;

            // Count by polytechnic
            $polytechnicName = $placement->student?->polytechnicInfo?->name ?? 'Unknown';
            $byPolytechnic[$polytechnicName] = ($byPolytechnic[$polytechnicName] ?? 0) + 1;

            // Count by session
            $session = $placement->student?->polytechnic_session ?? 'Unknown';
            $bySession[$session] = ($bySession[$session] ?? 0) + 1;
        }

        // Prepare employment data
        $salaryRanges = [
            'below_20k' => 0,
            '20k_40k' => 0,
            '40k_60k' => 0,
            '60k_100k' => 0,
            'above_100k' => 0,
        ];

        foreach ($placements as $placement) {
            if ($placement->present_status_type === 'App\Models\Employment' && $placement->status) {
                $salary = (int)($placement->status->salary ?? 0);
                if ($salary < 20000) {
                    $salaryRanges['below_20k']++;
                } elseif ($salary < 40000) {
                    $salaryRanges['20k_40k']++;
                } elseif ($salary < 60000) {
                    $salaryRanges['40k_60k']++;
                } elseif ($salary < 100000) {
                    $salaryRanges['60k_100k']++;
                } else {
                    $salaryRanges['above_100k']++;
                }
            }
        }

        return [
            'totalPlacements' => count($placements),
            'byStatus' => $statusCounts,
            'byMadrasah' => $byMadrasah,
            'byPolytechnic' => $byPolytechnic,
            'bySession' => $bySession,
            'salaryRanges' => $salaryRanges,
        ];
    }
    /**
     * Compute a variety of statistics grouped by session.
     *
     * @return array
     */
    private function computeSessionStatistics($filterSession = null): array
    {
        if ($filterSession) {
            $sessions = [$filterSession];
        } else {
            $sessions = AcademicSession::query()->pluck('session')->toArray();
        }
        $stats = [];

        foreach ($sessions as $session) {
            // count madrasah starters (ssc_session)
            $starterCount = Student::where('ssc_session', $session)->count();

            // madrasah pass/fail counts
            $passCount = MadrasahResult::whereHas('student', function ($q) use ($session) {
                $q->where('ssc_session', $session);
            })->where('status', 'Pass')->count();
            $failCount = MadrasahResult::whereHas('student', function ($q) use ($session) {
                $q->where('ssc_session', $session);
            })->where('status', 'Fail')->count();

            // polytechnic admitted count
            $admitCount = Student::where('polytechnic_session', $session)->count();

            // polytechnic dropout/completion counts
            $dropout = Result::whereHas('student', function ($q) use ($session) {
                $q->where('polytechnic_session', $session);
            })->where('status', 'Dropout')->count();
            $complete = Result::whereHas('student', function ($q) use ($session) {
                $q->where('polytechnic_session', $session);
            })->where('status', '<>', 'Dropout')->count();

            $stats[$session] = [
                'madrasa_starters' => $starterCount,
                'madrasa_pass' => $passCount,
                'madrasa_fail' => $failCount,
                'polytechnic_admit' => $admitCount,
                'polytechnic_dropout' => $dropout,
                'polytechnic_complete' => $complete,
            ];
        }

        return $stats;
    }
}
