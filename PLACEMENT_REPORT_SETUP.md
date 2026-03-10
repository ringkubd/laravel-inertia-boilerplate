<?php
/**
 * PLACEMENT REPORT IMPLEMENTATION GUIDE
 * =====================================
 * 
 * This file provides step-by-step instructions and code snippets
 * for implementing the Placement Report feature.
 */

?>

STEP 1: UPDATE THE CONTROLLER
================================

File: app/Http/Controllers/PlacementStatusController.php

Add at the TOP of the file (in the imports section):
---

<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Models\Employment;
use App\Models\FurtherEducation;
use App\Models\OtherPlacementStatus;
use App\Models\Placement;
use App\Models\Madrasha;          // ADD THIS
use App\Models\Polytechnic;       // ADD THIS
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

?>

---

Add this method to the PlacementStatusController class (after the index() method):

<?php
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
    $madrasahs = Madrasha::query()->pluck('name', 'id')->toArray();
    $polytechnics = Polytechnic::query()->pluck('name', 'id')->toArray();
    
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
?>

---

STEP 2: UPDATE ROUTES
========================

File: routes/web.php

Find the placement routes section (around line 266) and add the report route:

<?php
// Placement Status
Route::resource('placement', \App\Http\Controllers\PlacementStatusController::class);
Route::get('placement-report', [\App\Http\Controllers\PlacementStatusController::class, 'report'])->name('placement.report');   // ADD THIS LINE
?>

---

STEP 3: INSTALL CHART.JS DEPENDENCY
=====================================

Run this command in your terminal:

npm install chart.js

This installs Chart.js which is used for rendering the charts in the report.

---

STEP 4: ADD LINK TO PLACEMENT INDEX
===================================

File: resources/js/Pages/Placement/Index.vue

Add a link to the report in the card header section:

Update the card-header component call from:

<card-header
    :create="route('placement.create')"
    :searchMethod="search"
    :can="can"
></card-header>

To:

<div class="flex gap-2">
    <card-header
        :create="route('placement.create')"
        :searchMethod="search"
        :can="can"
    ></card-header>
    <a :href="route('placement.report')" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
        <i class="fas fa-chart-bar mr-2"></i>View Report
    </a>
</div>

---

STEP 5: VERIFY MODELS EXIST
============================

Make sure these models exist in app/Models/:
- Madrasha.php (or check the exact name in your codebase)
- Polytechnic.php

If the names are different, update the imports in the controller.

---

FEATURES INCLUDED
=================

✓ Filter by Session, Madrasah, and Polytechnic
✓ Summary statistics (Total, Employed, Higher Study, Other)
✓ Four interactive charts:
  - Status Distribution (Doughnut chart)
  - Salary Ranges (Bar chart)
  - Placements by Madrasah (Horizontal bar chart)
  - Placements by Session (Bar chart)
✓ Detailed data table with all placement information
✓ Print functionality with formatted output
✓ Responsive design
✓ Real-time chart updates on filter change

---

CHART TYPES USED
================

1. Status Distribution: Doughnut Chart
   - Shows count of Employed, Higher Study, Other

2. Salary Distribution: Horizontal Bar Chart
   - Shows distribution across 5 salary ranges

3. Madrasah Distribution: Horizontal Bar Chart
   - Shows placements by each madrasah

4. Session Distribution: Bar Chart
   - Shows placements by academic session

---

If you have any questions or need to customize the report, refer to:
- Vue 3 documentation: https://vuejs.org
- Chart.js documentation: https://www.chartjs.org
- Laravel Inertia documentation: https://inertiajs.com

