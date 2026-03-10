PLACEMENT REPORT SYSTEM - ARCHITECTURE & FLOW
==============================================


🏗️ SYSTEM ARCHITECTURE
─────────────────────────────────────────────────────────────────

┌─────────────────────────────────────────────────────────────┐
│                    USER INTERFACE LAYER                      │
│                     (Vue 3 Component)                        │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  ┌──────────────────┐                                       │
│  │  Filter Controls │                                       │
│  ├──────────────────┤                                       │
│  │• Session SELECT  │                                       │
│  │• Madrasah SELECT │                                       │
│  │• Polytechnic SEL │                                       │
│  │• Apply / Reset   │                                       │
│  └────────┬─────────┘                                       │
│           │                                                 │
│  ┌────────┴──────────────────────────────────────────┐     │
│  │  Summary Statistics Cards (4)                      │     │
│  │  [Total] [Employed] [Higher Study] [Other]        │     │
│  └────────┬──────────────────────────────────────────┘     │
│           │                                                 │
│  ┌────────┴──────────────────────────────────────────┐     │
│  │  Charts Section (2x2 Grid)                        │     │
│  │  ┌─────────────┬──────────────────┐               │     │
│  │  │  Status of  │  Salary Range    │               │     │
│  │  │  Doughnut   │  Horizontal Bar  │               │     │
│  │  ├─────────────┼──────────────────┤               │     │
│  │  │  By Madra   │  By Session      │               │     │
│  │  │  H. Bar     │  Vertical Bar    │               │     │
│  │  └─────────────┴──────────────────┘               │     │
│  └────────┬──────────────────────────────────────────┘     │
│           │                                                 │
│  ┌────────┴──────────────────────────────────────────┐     │
│  │  Data Table (Scrollable)                          │     │
│  │  [ID] [Name] [School] [Institute] [Session] [Status] │   │
│  └────────┬──────────────────────────────────────────┘     │
│           │                                                 │
│  ┌────────┴──────────────────┐                           │
│  │    Print Button            │                           │
│  │    Generate PDF Output     │                           │
│  └───────────────────────────┘                           │
│                                                              │
└──────────────────┬──────────────────────────────────────────┘
                   │
                   │ HTTP POST/GET
                   │ (router.get with filters)
                   ▼

┌─────────────────────────────────────────────────────────────┐
│                     ROUTING LAYER                           │
│                   (Inertia.js Routes)                       │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  Route::get('placement-report', ...)                        │
│      → PlacementStatusController@report                     │
│      → Inertia::render('Placement/Report', $data)          │
│                                                              │
└──────────────────┬──────────────────────────────────────────┘
                   │
                   │ HTTP Request
                   │ GET /placement-report?filters...
                   ▼

┌─────────────────────────────────────────────────────────────┐
│                   CONTROLLER LAYER                          │
│             (PlacementStatusController.php)                 │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  report(Request $request): Response {                       │
│    1. Build base query                                      │
│    2. Apply filters:                                        │
│       ├─ madrasah_id                                        │
│       ├─ polytechnic_id                                     │
│       └─ session                                            │
│    3. Execute query                                         │
│    4. Aggregate data                                        │
│    5. Get dropdown options                                  │
│    6. Render view with data                                 │
│  }                                                          │
│                                                              │
│  aggregatePlacementData($placements): array {              │
│    1. Count by status type                                 │
│    2. Group by madrasah                                    │
│    3. Group by polytechnic                                 │
│    4. Group by session                                     │
│    5. Calculate salary ranges                              │
│    6. Return structured array                              │
│  }                                                          │
│                                                              │
└──────────────────┬──────────────────────────────────────────┘
                   │
                   │ Eloquent Query
                   │ Placement::with(...)->get()
                   ▼

┌─────────────────────────────────────────────────────────────┐
│                   MODEL & DATABASE LAYER                    │
│                    (Eloquent ORM)                           │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  Placement Model                                            │
│  ├─ Has One: Status (Polymorphic)                          │
│  │  ├─ Employment                                           │
│  │  ├─ FurtherEducation                                    │
│  │  └─ OtherPlacementStatus                                │
│  └─ Belongs To: Student                                    │
│                                                              │
│  Student Model                                              │
│  ├─ Belongs To: Madrasha (madrasah_id)                    │
│  ├─ Belongs To: Polytechnic (polytechnic_id)              │
│  └─ Has Many: Placements                                   │
│                                                              │
│  Database Tables:                                           │
│  placements                                                 │
│  ├─ id                                                      │
│  ├─ student_id ──────────────┐                             │
│  ├─ present_status_id        │                             │
│  ├─ present_status_type      │                             │
│  └─ final_result             │                             │
│                              │                             │
│                    ┌─────────┘                             │
│                    │                                        │
│  students          │                                        │
│  ├─ id ◄───────────┘                                        │
│  ├─ name                                                    │
│  ├─ madrasha_id ─────────────┐                            │
│  ├─ polytechnic_id ───────┐  │                            │
│  │                         │  │                            │
│  └─ polytechnic_session    │  │                            │
│                            │  │                            │
│                  ┌─────────┘  │                            │
│                  │            │                            │
│  madrashas       │            │                            │
│  ├─ id ◄─────────┘            │                            │
│  ├─ name                      │                            │
│  └─ ...                       │                            │
│                               │                            │
│               ┌───────────────┘                            │
│               │                                             │
│  polytechnics│                                             │
│  ├─ id ◄─────┘                                             │
│  ├─ name                                                   │
│  └─ ...                                                    │
│                                                              │
│  employment                                                │
│  ├─ id                                                     │
│  ├─ student_id                                            │
│  ├─ organization                                          │
│  ├─ position                                              │
│  └─ salary                                                │
│                                                              │
│  further_educations                                        │
│  ├─ id                                                     │
│  ├─ student_id                                            │
│  ├─ institute_name                                        │
│  ├─ degree                                                │
│  └─ semester                                              │
│                                                              │
│  other_placement_statuses                                  │
│  ├─ id                                                     │
│  ├─ student_id                                            │
│  └─ note                                                  │
│                                                              │
└─────────────────────────────────────────────────────────────┘


📊 DATA FLOW DIAGRAM
─────────────────────────────────────────────────────────────

USER ACTION
    ↓
[Filter Form Submit]
    ↓
{session, madrasah_id, polytechnic_id}
    ↓
HTTP POST → router.get('placement.report', filters)
    ↓
Controller: report(Request $request)
    ↓
├─→ Build Query
│    ├─ Placement::query()
│    └─ with('status', 'student', ...)
    ├─→ Apply Filters
│    ├─ Filter by madrasah_id
│    ├─ Filter by polytechnic_id
│    └─ Filter by session
    ├─→ Execute Query
│    └─ ->latest()->get()
    ├─→ Aggregate Data
│    ├─ aggregatePlacementData($placements)
│    ├─ Count by status
│    ├─ Group by madrasah/polytechnic/session
│    └─ Calculate salary ranges
    └─→ Get Options
        ├─ AcademicSession::pluck()
        ├─ Madrasha::pluck()
        └─ Polytechnic::pluck()
    ↓
Inertia::render('Placement/Report', $data)
    ↓
{
  placements: [...],
  reportData: {...},
  sessions: [...],
  madrasahs: {...},
  polytechnics: {...},
  filters: {...}
}
    ↓
HTTP Response → JSON (Inertia Format)
    ↓
Vue Component: Report.vue
    ↓
mounted() → initializeCharts()
    ↓
├─→ renderStatusChart()      [Doughnut]
├─→ renderSalaryChart()      [Bar]
├─→ renderMadrasahChart()    [H-Bar]
└─→ renderSessionChart()     [Bar]
    ↓
Chart.js Rendering
    ↓
Display on Page
    ↓
User Views:
├─→ Filter Controls
├─→ Summary Cards
├─→ 4 Charts
├─→ Data Table
└─→ Print Button
    ↓
User Action: Print
    ↓
printReport() Method
    ↓
window.open() → New Window
    ↓
Formatted HTML
    ↓
window.print() → Print Dialog
    ↓
User: Print to PDF/Printer


🔄 FILTER FLOW
─────────────────────────────────────────────────────────────

┌─────────────────────────┐
│  User Selects Filters   │
│  (Session/Madrasah/Poly)│
└────────┬────────────────┘
         │
         ▼
┌─────────────────────────┐
│ applyFilters() Method   │
├─────────────────────────┤
│ this.filters = {...}    │
│ router.get(route...) ← form data
└────────┬────────────────┘
         │
         ▼
┌──────────────────────────────────┐
│ Controller: report() Method       │
├──────────────────────────────────┤
│ Check Each Filter:               │
│ ├─ IF madrasah_id provided      │
│ │  └─ Filter by madrasah_id     │
│ │  └─ Else use user's default   │
│ │                                │
│ ├─ IF polytechnic_id provided   │
│ │  └─ Filter by polytechnic_id  │
│ │                                │
│ └─ IF session provided          │
│    └─ Filter by session         │
└────────┬───────────────────────┘
         │
         ▼
┌──────────────────────────────────┐
│ Execute Filtered Query            │
│ Build WHERE conditions            │
│ Apply multiple filters together   │
│ Get matching placements           │
└────────┬───────────────────────┘
         │
         ▼
┌──────────────────────────────────┐
│ Return Results to Vue             │
│ Update Component Data             │
│ Alert watcher: reportData         │
└────────┬───────────────────────┘
         │
         ▼
┌──────────────────────────────────┐
│ Vue Watcher: reportData()         │
│ Calls: initializeCharts()         │
└────────┬───────────────────────┘
         │
         ▼
┌──────────────────────────────────┐
│ Charts Re-render                  │
│ Table Data Updates                │
│ Summary Cards Update              │
│ Fresh Visual Display              │
└──────────────────────────────────┘


💾 STATE MANAGEMENT
─────────────────────────────────────────────────────────────

Vue Component State:
┌────────────────────────────────┐
│ Component Data Properties       │
├────────────────────────────────┤
│ filters: {                       │
│   session: '',                   │
│   madrasah_id: '',              │
│   polytechnic_id: ''            │
│ }                                │
│                                  │
│ charts: {                        │
│   statusChart: null,            │
│   salaryChart: null,            │
│   madrasahChart: null,          │
│   sessionChart: null            │
│ }                                │
└────────────────────────────────┘

Props from Controller:
┌────────────────────────────────┐
│ placements: Array              │
│ reportData: Object             │
│ sessions: Array                │
│ madrasahs: Object              │
│ polytechnics: Object           │
│ filters: Object                │
└────────────────────────────────┘

Computed/Reactive:
├─ Filter values (v-model)
├─ Form data
└─ Chart instances


🎨 COMPONENT HIERARCHY
─────────────────────────────────────────────────────────────

Report.vue
├─ Template
│  ├─ Head (page title)
│  ├─ BreezeAuthenticatedLayout (wrapper)
│  │  ├─ Header (page title)
│  │  └─ Main Content
│  │     ├─ Filter Card
│  │     │  ├─ Form
│  │     │  └─ Buttons (Apply, Reset, Print)
│  │     ├─ Summary Cards (4x)
│  │     ├─ Charts Grid (2x2)
│  │     │  ├─ Status Chart
│  │     │  ├─ Salary Chart
│  │     │  ├─ Madrasah Chart
│  │     │  └─ Session Chart
│  │     ├─ Data Table
│  │     │  ├─ Header Row
│  │     │  └─ Data Rows (v-for)
│  │     └─ Print Area (hidden)
│  │
│  └─ Styles (scoped CSS)
│
├─ Script (Vue Logic)
│  ├─ Components imported
│  ├─ Props definition
│  ├─ Data properties
│  ├─ Lifecycle (mounted)
│  ├─ Methods
│  │  ├─ applyFilters()
│  │  ├─ resetFilters()
│  │  ├─ initializeCharts()
│  │  ├─ renderStatusChart()
│  │  ├─ renderSalaryChart()
│  │  ├─ renderMadrasahChart()
│  │  ├─ renderSessionChart()
│  │  ├─ getStatusText()
│  │  ├─ getStatusBadgeClass()
│  │  └─ printReport()
│  │
│  └─ Watchers
│     └─ reportData()


═════════════════════════════════════════════════════════════════

This architecture ensures:
✓ Clear separation of concerns
✓ Efficient data flow
✓ Reusable components
✓ Maintainable code
✓ Scalable design
✓ Performance optimization

═════════════════════════════════════════════════════════════════
