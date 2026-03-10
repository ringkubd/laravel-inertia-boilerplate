<?php
/**
 * Placement Report Setup Script
 * This script generates the necessary code additions for the placement report feature
 */

echo "=== PLACEMENT REPORT SETUP ===\n\n";

// 1. Controller Method to Add
$controllerMethod = <<<'PHP'
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

    // Get filter options
    $sessions = AcademicSession::query()->pluck('session')->toArray();
    $madrasahs = \App\Models\Madrasha::query()->pluck('name', 'id')->toArray();
    $polytechnics = \App\Models\Polytechnic::query()->pluck('name', 'id')->toArray();

    return Inertia::render('Placement/Report', [
        'placements' => $placements,
        'reportData' => $reportData,
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
        $polytechnicName = $placement->student?->polytechnic_info?->name ?? 'Unknown';
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
PHP;

echo "1. ADD THIS METHOD TO PlacementStatusController:\n";
echo str_repeat("-", 80) . "\n";
echo $controllerMethod . "\n";
echo str_repeat("-", 80) . "\n\n";

// 2. Route to add
$routeCode = <<<'ROUTE'
// Add this to routes/web.php in the placement routes section:
Route::get('placement-report', [\App\Http\Controllers\PlacementStatusController::class, 'report'])->name('placement.report');
ROUTE;

echo "2. ADD THIS ROUTE:\n";
echo str_repeat("-", 80) . "\n";
echo $routeCode . "\n";
echo str_repeat("-", 80) . "\n\n";

// 3. NPM Package
$npmPackage = <<<'NPM'
3. INSTALL CHARTING LIBRARY:
   Run: npm install chart.js vue-chartjs

   This will add Chart.js and Vue Chart.js wrapper to your project.
NPM;

echo $npmPackage . "\n";
echo str_repeat("-", 80) . "\n\n";

echo "✓ Setup instructions generated successfully!\n";
echo "Next: Create the Vue component and update routes.\n";
