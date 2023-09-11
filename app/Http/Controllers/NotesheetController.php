<?php

namespace App\Http\Controllers;

use App\Http\Resources\InvoiceResource;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\Notesheet;
use App\Models\NotesheetTemplate;
use App\Models\NotesheetText;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;

class NotesheetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $note_sheet = Notesheet::query()
            ->with(['invoice' => function($q){
                $q->selectRaw('invoice_id,invoice_no,session, sum(amount) as total_amount, invoice_month,invoice_date, semester, count(student_id) as number_of_student,page_no,serial_no, fee_type')->groupBy('invoice_id');
            }])
            ->whereHas('invoice')
            ->latest()
            ->paginate();
        return Inertia::render('NoteSheet/Index', [
            'note_sheet' => $note_sheet,
            'can' => [
                'create' => auth()->user()->can('create_note_sheet'),
                'update' => auth()->user()->can('delete_note_sheet'),
                'delete' => auth()->user()->can('update_note_sheet'),
                'view' => auth()->user()->can('view_note_sheet'),
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $invoice = Invoice::query()
            ->selectRaw('
            SUM(IF(results.status is null, 1, 0)) as number_of_student,
            SUM(IF(amount > 0, 1, 0 )) as total_student,
            (SUM(IF(results.status is null, 1, 0)) - SUM(IF(amount > 0, 1, 0 ))) as nd_student,
            (count(invoices.student_id) - SUM(IF(results.status is null, 1, 0))) as dropout_student,
            sum(amount) as total_amount,
            invoice_month,
            invoices.session,
            invoice_id,
            invoice_no,
            invoice_date,
            invoices.semester,
            page_no,
            serial_no,
            fee_type
            ')
            ->whereNull('notesheet_id')
            ->whereNull('page_no')
            ->whereNull('serial_no')
            ->leftJoin('results', function ($join){
                $join->on('results.student_id', 'invoices.student_id')->where('status', 'Dropout');
            })
            ->groupBy('invoice_id')
            ->whereRaw('date(invoices.created_at) > "2023-07-01"')
            ->latest('invoices.created_at')
            ->get();
        $notesheetTemplate = NotesheetTemplate::all();
        $noteSheetText = NotesheetText::query()->get()->groupBy('note_type');
        $pageNo = newPageNo();

        $serialNo = newPageNo('serial_no');

        return Inertia::render('NoteSheet/Generate', [
            'invoices' => $invoice,
            'note_sheet_template' => $notesheetTemplate,
            'page_no' => $pageNo,
            'serial_no' => $serialNo,
            'noteSheetText' => $noteSheetText,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'note_text' => 'required',
            'page_no' => 'required',
            'serial_no' => 'required',
            'invoice_id' => 'required'
        ]);
        $data = $request->all();
        $data['user_id'] = auth()->user()->id;
        if($note = Notesheet::create($data)){
            Invoice::where('invoice_id', $request->invoice_id)->update([
                'page_no' => $request->page_no,
                'serial_no' => $request->serial_no,
                'notesheet_id' => $note->id,
            ]);
        }

        return redirect()->route('note_sheet.index')->withSuccess("Note sheet created successfully");
    }

    /**
     * Display the specified resource.
     *
     * @param Notesheet $notesheet
     * @return Response
     */
    public function show(Notesheet $note_sheet)
    {
        return Inertia::render('NoteSheet/Preview', [
            'note_sheet' => $note_sheet
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Notesheet $notesheet
     * @return Response
     */
    public function edit(Notesheet $notesheet)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Notesheet $notesheet
     * @return Response
     */
    public function update(Request $request, Notesheet $notesheet)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Notesheet $notesheet
     * @return Response
     */
    public function destroy(Notesheet $note_sheet)
    {
        $invoice = Invoice::query()
            ->where('invoice_id', $note_sheet->invoice_id)
            ->update([
                'notesheet_id' => null,
                'page_no' => null,
                'serial_no' => null
            ]);
        $note_sheet->delete();
        return redirect()->route('note_sheet.index')->withSuccess("Note sheet deleted successfully");
    }

    public function getInvoiceInfo($invoiceId = ""){
        $invoice = Invoice::query()
            ->selectRaw('count(student_id) as number_of_student, SUM(IF(amount > 0, 1, 0 )) as total_student, sum(amount) as total_amount, invoice_month, session, invoice_id, invoice_no, invoice_date, semester,page_no,serial_no, fee_type')
            ->whereNull('notesheet_id')
            ->whereNull('page_no')
            ->whereNull('serial_no')
            ->where('invoice_id',$invoiceId)
            ->groupBy('invoice_id')
            ->first();

        if ($invoice) {
            return response()->json($invoice);
        }
        return response()->json([]);
    }

    public function mmaTable($invoiceId){
        $invoice = InvoiceDetail::query()
            ->selectRaw('
            SUM(IF(student_amount > 0, 1, 0)) as mma_student,
            SUM(IF(board_amount > 0, 1, 0)) as book_student,
            sum(student_amount) as total_student_amount,
            max(student_amount) as student_amount,
            sum(board_amount) as total_board_amount,
            board_amount,
            sum(institute_amount) as total_institute_amount,
            institute_amount,
            SUM(amount) as amount,
            SUM(IF(amount = 0 , 1, 0)) as failed_student,
            SUM(IF(amount > 0 , 1, 0)) as eligible_student,
            invoice_month, invoice'
            )
            ->where('invoice', $invoiceId)
            ->groupBy('fee_type')
            ->firstOrFail();
        $mma = number_format(2000 * $invoice->eligible_student, 2, '.', ',');
        $book_stationary = number_format(500 * $invoice->eligible_student, 2, '.', ',');
        $total = number_format(2500 * $invoice->eligible_student, 2, '.', ',');
        $table =  <<<TEMPLATE
<table class="table table-bordered mt-4 text-center">
        <thead>
        <tr>
            <th>Payment Against</th>
            <th>Number of Students</th>
            <th>Taka / Month</th>
            <th>Sub. Total (TK.)</th>
            <th>Total (TK.)</th>
            <th>Period</th>
        </tr>
        </thead>
        <tbody class="text-center align-middle" style="text-align: center">
        <tr>
            <th>MMA</th>
            <td>
                $invoice->eligible_student
            </td>
            <td> 2,000 </td>
            <td> $mma </td>
            <td rowspan="2">$total</td>
            <td rowspan="2"> $invoice->invoice_month </td>
        </tr>
        <tr>
            <th>Book & Stationary</th>
            <td>
                $invoice->eligible_student
            </td>
            <td>500</td>
            <td>$book_stationary</td>
        </tr>
        </tbody>
    </table>
TEMPLATE;

        return [
            'table' => $table,
            'invoice' => $invoice
        ];
    }

    public function admissionTable($invoiceId){
        $invoices = InvoiceDetail::query()
            ->selectRaw('invoice_details.*, students.polytechnic_trade_id')
            ->where('invoice', $invoiceId)
            ->join('students', 'invoice_details.student_id', 'students.id')
            ->get();
        $invoice = $invoices->groupBy('polytechnic_trade_id');
        $totalBoardFee = number_format($invoices->sum('board_amount'), 0, '.', ',');
        $totalInstituteFee = number_format($invoices->sum('institute_amount'), 0, '.', ',');
        $totalAmount =  number_format($invoices->sum('amount'), 0, '.', ',');
        $totalStudent = number_format($invoices->groupBy('student_id')->count('student_id'), 0, '.', ',');
        $template = <<<TEMPLATE
<table class="table table-bordered mt-4" id="admission_table">
        <thead>
        <tr>
            <th>SL.#</th>
            <th>Technology</th>
            <th># Students</th>
            <th>Adm. Fee (BTEB) (TK.)</th>
            <th>Adm. Fee (Ins.) (TK.)</th>
            <th>Total (TK.)</th>
        </tr>
        </thead>
        <tbody class="text-center align-middle">
TEMPLATE;
        $i = 0;
        foreach ($invoice as $technology => $inv){
            $numberOfStudent = number_format($inv->groupBy('student_id')->count(), 0, '.', ',');
            $board_amount = number_format($inv->sum('board_amount'), 0, '.', ',');
            $institute_amount = number_format($inv->sum('institute_amount'), 0, '.', ',');
            $total_amount = number_format($inv->sum('amount'), 0, '.', ',');
            $i++;
            $template .= <<<TEMPLATE
        <tr class="m-0">
            <td>
                $i
            </td>
            <td style="text-align: left">$technology</td>
            <td>$numberOfStudent</td>
            <td>$board_amount</td>
            <td>$institute_amount</td>
            <td>$total_amount</td>
        </tr>
TEMPLATE;
        }
        $template .= <<<TEMPLATE
<tr>
<th colspan="2" class="text-right">Total</th>
<td>$totalStudent</td>
<td>$totalBoardFee</td>
<td>$totalInstituteFee</td>
<td>$totalAmount</td>
</tr>
TEMPLATE;


        $template .= "</tbody>
    </table>";
        return $template;

    }
}
