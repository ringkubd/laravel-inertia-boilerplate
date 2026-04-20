<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Models\FeeType;
use App\Models\Notesheet;
use App\Models\NotesheetTemplate;
use App\Services\MadrasahNotesheetDataService;
use App\Services\NotesheetTemplateParser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MadrasahNotesheetController extends Controller
{
    public function __construct(
        private MadrasahNotesheetDataService $dataService,
        private NotesheetTemplateParser $templateParser
    ) {}

    public function index(): Response
    {
        $notesheets = Notesheet::query()
            ->madrasah()
            ->latest()
            ->paginate();

        return Inertia::render('NoteSheet/Madrasah/Index', [
            'notesheets' => $notesheets,
        ]);
    }

    public function create(): Response
    {
        $templates = NotesheetTemplate::query()->select('id', 'title', 'content')->latest()->get();
        $academicSessions = AcademicSession::query()->select('id', 'session')->get();
        $feeTypes = FeeType::query()->where('is_madrasa', 1)->select('id', 'name')->get();

        return Inertia::render('NoteSheet/Madrasah/Create', [
            'templates' => $templates,
            'academic_sessions' => $academicSessions,
            'fee_types' => $feeTypes,
            'page_no' => newMadrasahPageNo(),
            'serial_no' => newMadrasahPageNo('serial_no'),
        ]);
    }

    public function preview(Request $request): JsonResponse
    {
        $data = $request->validate([
            'academic_session' => 'required|string',
            'note_type' => 'required|in:class_9_registration,class_9_exam,class_10_exam,teacher_salary',
            'template_id' => 'nullable|exists:notesheet_templates,id',
            'bteb_circular_date' => 'nullable|date',
            'exam_payment_deadline' => 'nullable|date',
            'attachment_payment_deadline' => 'nullable|date',
            'fee_per_student' => 'nullable|integer|min:0',
            'center_fee_per_student' => 'nullable|integer|min:0',
            'attachment_fee_per_student' => 'nullable|integer|min:0',
            'serial_no' => 'nullable|integer|min:1',
        ]);

        $feeOverride = isset($data['fee_per_student']) ? (int)$data['fee_per_student'] : null;
        $centerOverride = isset($data['center_fee_per_student']) ? (int)$data['center_fee_per_student'] : null;
        $attachmentOverride = isset($data['attachment_fee_per_student']) ? (int)$data['attachment_fee_per_student'] : null;
        $tableData = $this->dataService->generate($data['academic_session'], $data['note_type'], $feeOverride, $centerOverride, $attachmentOverride);
        $template = NotesheetTemplate::query()->find($data['template_id']);

        $templateContent = $template?->content ?: '<p>Notesheet prepared for {{ session_year }}.</p>';

        $paraStart = $data['note_type'] === 'class_9_registration'
            ? 146
            : (isset($data['serial_no']) ? (int)$data['serial_no'] : newMadrasahPageNo('serial_no'));
        $madrasahCount = count($tableData['madrasah_groups']);

        $feeStructure = $tableData['fee_structure'];
        $perStudentTotal = $feeStructure['bteb'] + $feeStructure['center'] + $feeStructure['attachment'];

        // Keep Table-1 breakdown rows consistent with the current per-student fee inputs.
        $btebExamComponent = (int) round(($feeStructure['bteb'] * 800) / 960);
        $btebNumberCardComponent = (int) round(($feeStructure['bteb'] * 80) / 960);
        $btebAttachmentComponent = (int) ($feeStructure['bteb'] - $btebExamComponent - $btebNumberCardComponent);

        $centerMainComponent = (int) round(($feeStructure['center'] * 550) / 945);
        $centerPracticalCenterComponent = (int) round(($feeStructure['center'] * 150) / 945);
        $centerPracticalComponent = (int) ($feeStructure['center'] - $centerMainComponent - $centerPracticalCenterComponent);

        $parsedText = $this->templateParser->parse($templateContent, [
            'session_year' => $tableData['academic_session'],
            'total_students' => $tableData['total_students'],
            'per_student_fee' => number_format($perStudentTotal),
            'bteb_fee' => number_format($feeStructure['bteb']),
            'center_fee' => number_format($feeStructure['center']),
            'attachment_fee' => number_format($feeStructure['attachment']),
            'bteb_exam_component_fee' => number_format($btebExamComponent),
            'bteb_number_card_component_fee' => number_format($btebNumberCardComponent),
            'bteb_attachment_component_fee' => number_format($btebAttachmentComponent),
            'center_main_component_fee' => number_format($centerMainComponent),
            'center_practical_center_component_fee' => number_format($centerPracticalCenterComponent),
            'center_practical_component_fee' => number_format($centerPracticalComponent),
            'grand_total' => number_format($tableData['totals']['total']),
            'current_date' => now()->format('d F Y'),
            'page_no' => newMadrasahPageNo(),
            'serial_no' => $paraStart,
            'bteb_circular_date' => !empty($data['bteb_circular_date']) ? \Carbon\Carbon::parse($data['bteb_circular_date'])->format('d F Y') : '',
            'exam_payment_deadline' => !empty($data['exam_payment_deadline']) ? \Carbon\Carbon::parse($data['exam_payment_deadline'])->format('d F Y') : '',
            'attachment_payment_deadline' => !empty($data['attachment_payment_deadline']) ? \Carbon\Carbon::parse($data['attachment_payment_deadline'])->format('d F Y') : '',
            'madrasah_count' => $madrasahCount,
            'para_1' => $paraStart,
            'para_2' => $paraStart + 1,
            'para_3' => $paraStart + 2,
            'para_4' => $paraStart + 3,
        ]);

        return response()->json([
            'parsed_text' => $parsedText,
            'table_data' => $tableData,
            'columns' => $this->columnsForType($data['note_type']),
            'para_start' => $paraStart,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'note_text' => 'required|string',
            'page_no' => 'required|integer',
            'serial_no' => 'required|integer',
            'note_type' => 'required|in:class_9_registration,class_9_exam,class_10_exam,teacher_salary',
            'academic_session' => 'required|string',
            'meta_data' => 'nullable|array',
        ]);

        $paraStart = (int)$data['serial_no'];
        $paraEnd = $paraStart + $this->paraCount($data['note_type']) - 1;

        $payload = [
            'note_text' => $data['note_text'],
            'page_no' => $data['page_no'],
            'serial_no' => $paraEnd,
            'invoice_id' => null,
            'user_id' => auth()->id(),
            'notesheet_category' => $this->categoryByType($data['note_type']),
            'meta_data' => $data['meta_data'] ?? [
                'academic_session' => $data['academic_session'],
                'note_type' => $data['note_type'],
            ],
        ];

        Notesheet::query()->create($payload);

        return redirect()
            ->route('madrasah_notesheet.index')
            ->withSuccess('Madrasah notesheet created successfully');
    }

    private function paraCount(string $noteType): int
    {
        return match ($noteType) {
            'class_9_registration' => 3,
            'class_9_exam', 'class_10_exam' => 2,
            default => 2,
        };
    }

    private function categoryByType(string $noteType): string
    {
        return match ($noteType) {
            'class_9_registration' => 'madrasah_registration',
            'teacher_salary' => 'teacher_salary',
            default => 'madrasah_exam',
        };
    }

    private function columnsForType(string $noteType): array
    {
        if ($noteType === 'class_9_registration') {
            return [
                'show_bteb' => true,
                'show_center' => false,
                'show_attachment' => false,
                'is_registration' => true,
            ];
        }

        if ($noteType === 'teacher_salary') {
            return [
                'show_bteb' => false,
                'show_center' => false,
                'show_attachment' => false,
            ];
        }

        return [
            'show_bteb' => true,
            'show_center' => true,
            'show_attachment' => true,
        ];
    }
}
