<?php

namespace App\Http\Controllers;

use App\Http\Resources\InvoiceResource;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\Notesheet;
use App\Models\NotesheetTemplate;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NotesheetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $invoice = Invoice::query()
            ->selectRaw('count(student_id) as number_of_student, sum(amount) as total_amount, invoice_month, session, invoice_id, invoice_no, invoice_date, semester,page_no,serial_no, fee_type')
            ->whereNull('notesheet_id')
            ->groupBy('invoice_id')
            ->get();
        return Inertia::render('NoteSheet/Index', [
            'invoices' => $invoice
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $invoice = Invoice::query()
            ->selectRaw('count(student_id) as number_of_student, sum(amount) as total_amount, invoice_month, session, invoice_id, invoice_no, invoice_date, semester,page_no,serial_no, fee_type')
            ->whereNull('notesheet_id')
            ->whereNull('page_no')
            ->whereNull('serial_no')
            ->groupBy('invoice_id')
            ->get();

        $notesheetTemplate = NotesheetTemplate::all();
        $pageNo = newPageNo();
        $serialNo = newPageNo('serial_no');
        return Inertia::render('NoteSheet/Generate', [
            'invoices' => $invoice,
            'note_sheet_template' => $notesheetTemplate,
            'page_no' => $pageNo,
            'serial_no' => $serialNo
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
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
        $notesheet = Notesheet::create($data);
        return redirect()->route('note_sheet.index')->withSuccess("Note sheet created successfully");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Notesheet  $notesheet
     * @return \Illuminate\Http\Response
     */
    public function show(Notesheet $notesheet)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Notesheet  $notesheet
     * @return \Illuminate\Http\Response
     */
    public function edit(Notesheet $notesheet)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Notesheet  $notesheet
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Notesheet $notesheet)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Notesheet  $notesheet
     * @return \Illuminate\Http\Response
     */
    public function destroy(Notesheet $notesheet)
    {
        //
    }

    public function getInvoiceInfo($invoiceId = ""){
        $invoice = Invoice::query()
            ->selectRaw('count(student_id) as number_of_student, sum(amount) as total_amount, invoice_month, session, invoice_id, invoice_no, invoice_date, semester,page_no,serial_no, fee_type')
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
            ->selectRaw('SUM(IF(student_amount > 0, 1, 0)) as mma_student, SUM(IF(board_amount > 0, 1, 0)) as book_student, sum(student_amount) as total_student_amount, max(student_amount) as student_amount, sum(board_amount) as total_board_amount, board_amount, sum(institute_amount) as total_institute_amount, institute_amount, sum(amount) as amount, invoice_month, invoice')
            ->where('invoice', $invoiceId)
            ->groupBy('fee_type')
            ->firstOrFail();
        return <<<TEMPLATE
<table class="table table-bordered border-t-1 mt-10" style="margin-top: 5rem; border-top: 2px solid black">
        <thead>
        <tr>
            <th>Number of Students</th>
            <th>Payment Against</th>
            <th>Taka / Month</th>
            <th>Period</th>
            <th>Sub. Total (TK.)</th>
            <th>Total (TK.)</th>
        </tr>
        </thead>
        <tbody class="text-center align-middle">
        <tr>
            <td>
                $invoice->mma_student
            </td>
            <th>MMA</th>
            <td> $invoice->student_amount </td>
            <td rowspan="2"> $invoice->invoice_month </td>
            <td> $invoice->total_student_amount </td>
            <td rowspan="2">$invoice->amount </td>
        </tr>
        <tr>
            <td>
                $invoice->book_student
            </td>
            <td>Book & Stationary</td>
            <td>$invoice->board_amount</td>
            <td>$invoice->total_board_amount</td>
        </tr>
        </tbody>
    </table>
TEMPLATE;
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
        $totalStudent = number_format($invoices->count('student_id'), 0, '.', ',');
        $template = <<<TEMPLATE
<table class="table table-bordered border-t-1 mt-10" style="margin-top: 5rem; border-top: 2px solid black">
        <thead>
        <tr>
            <th>SL.#</th>
            <th>Technology</th>
            <th># Students</th>
            <th>Admission Fee (BTEB) (TK.)</th>
            <th>Admission Fee (Institute) (TK.)</th>
            <th>Total (TK.)</th>
        </tr>
        </thead>
        <tbody class="text-center align-middle">
TEMPLATE;
        $i = 0;
        foreach ($invoice as $technology => $inv){
            $numberOfStudent = number_format($inv->count(), 0, '.', ',');
            $board_amount = number_format($inv->sum('board_amount'), 0, '.', ',');
            $institute_amount = number_format($inv->sum('institute_amount'), 0, '.', ',');
            $total_amount = number_format($inv->sum('amount'), 0, '.', ',');
            $i++;
            $template .= <<<TEMPLATE
        <tr>
            <td>
                $i
            </td>
            <th style="text-align: left">$technology</th>
             <td>$numberOfStudent</td>
            <td>$board_amount</td>
            <td>$institute_amount</td>
            <td>$total_amount</td>
        </tr>
TEMPLATE;
        }
        $template .= <<<TEMPLATE
<tr>
<td colspan="2" class="text-right">Total</td>
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
    public function semesterTable($invoiceId){

    }
}
