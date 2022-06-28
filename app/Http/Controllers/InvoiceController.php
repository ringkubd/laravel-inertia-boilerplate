<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Models\Invoice;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use App\Models\InvoiceDetail;
use Ramsey\Uuid\Rfc4122\UuidV4;
use function Aws\map;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view_invoice');
        $invoices = Invoice::query()
            ->select('invoice_id', 'invoice_month', 'invoice_date', DB::raw( 'sum(amount) as total_amount'), 'fee_type', 'session', 'semester')
            ->when($request->search, function ($q, $v){
                $q->where('student_id', 'like', "%$v%")
                    ->orWhere('invoice_month', 'like', "%$v%")
                    ->orWhere('invoice_id', 'like', "%$v%")
                    ->orWhere('invoice_date', 'like', "%$v%")
                    ->orWhere('student_name', 'like', "%$v%");
            })
            ->orderByDesc('invoice_date')
            ->with('details')
            ->groupBy('invoice_id')
            ->paginate();
        return Inertia::render('Invoice/Index', [
            'can' => [
                'create' => auth()->user()->can('create_invoice'),
                'update' => auth()->user()->can('update_invoice'),
                'delete' => auth()->user()->can('delete_invoice'),
                'view' => auth()->user()->can('view_invoice'),
            ],
            'invoices' => $invoices
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $this->authorize('create_invoice');
        $sessions = AcademicSession::all();

        $students = Student::query()
            ->where('polytechnic_session', "$request->polytechnic_session")
            ->with(['fees' => function($q) use ($request){
                $q->where('fees.semester', $request->semester)
                    ->where('fees.session', "$request->polytechnic_session");
            }])
            ->when($request->semester, function ($q, $v) use($request){
                if ($v != 1) {
                    $q->whereHas('results', function($q) use($request){
                        $q->where('status','!=','Dropout')
                            ->where('semester', $request->semester);
                    })->where('polytechnic_session', "$request->polytechnic_session");
                }else{
                    $q->doesntHave('results')
                        ->orWhereHas('results', function ($q) use($request) {
                            $q->where('status','!=','Dropout')->where('semester', $request->semester);
                        })->where('polytechnic_session', "$request->polytechnic_session");
                }

            })
            ->with(['invoice' => function($q) use($request){
                $q->where('session', $request->polytechnic_session)
                    ->where('semester', $request->semester);
            }])
            ->with('invoiceDetails')
            ->get();
        $feeTypes = $students->whereNotNull('fees')->unique('fee_type')->max('fees');
        return Inertia::render('Invoice/Create', [
            'can' => [
                'create' => auth()->user()->can('create_invoice'),
                'update' => auth()->user()->can('update_invoice'),
                'delete' => auth()->user()->can('delete_invoice'),
                'view' => auth()->user()->can('view_invoice'),
            ],
            'students' => $students,
            'sessions' => $sessions,
            'feeTypes' => $feeTypes

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
        $this->authorize('create_invoice');
        $request->validate([
            'academic_session' => 'required',
            'semester' => 'required',
            'invoice_month' => 'required',
            'billableFee' => 'required',
        ]);
        $billableFee = collect(json_decode($request->billableFee))->toArray();
        $billableFee = array_filter($billableFee, function ($var){
            if ($var) {
                return $var;
            }
            return false;
        });

        $lastMMa = 0;
       if (array_key_exists('MMA',$billableFee)) {
           $lastMMa = lastMmaNo($request->academic_session, $request->semester);
       }

        $selected_student = collect(json_decode($request->selected_students))->toArray();
        $selected_student = array_filter($selected_student, function ($var){
            if ($var) {
                return $var;
            }
            return false;
        });

        $students = Student::query()
            ->with(['fees' => function($q) use ($request, $billableFee){
                $q->where('semester', $request->semester)
                    ->whereIn('fee_type', array_keys($billableFee));
            }])
            ->whereHas('fees', function ($q) use ($request){
                $q->where('semester', $request->semester);
            })
            ->when($request->semester != 1, function ($q) use($request){
                $q->whereHas('results', function($q) use($request){
                    $q->where('status','!=','Dropout')
                        ->where('semester', $request->semester);
                })->where('polytechnic_session', "$request->academic_session");
            },function ($q) use ($request){
                $q->doesntHave('results')
                    ->orWhereHas('results', function ($q) use($request) {
                        $q->where('status','!=','Dropout')->where('semester', $request->semester);
                    })->where('polytechnic_session', "$request->academic_session");
            })
            ->where('polytechnic_session', "$request->academic_session")
            ->whereIn('id', array_keys($selected_student))
            ->get();
        $invoiceId = rand(1111,99999);
        foreach($students as $student){
            $invoiceDetails = [];
            $feeType = [];
            foreach ($student->fees as $fee){
                $invoiceDetails[] = new InvoiceDetail([
                    'fee_type' => $fee->fee_type,
                    'amount' => $fee->amount,
                    'institute_amount' => $fee->institute,
                    'board_amount' => $fee->board,
                    'student_amount' => $fee->student,
                    'invoice' => $invoiceId,
                    'student_id' => $student->id,
                    'invoice_month' => $request->invoice_month,
                ]);
                $feeType[] = $fee->fee_type;
            }

            $invoice = Invoice::create([
                'invoice_id' => $invoiceId,
                'invoice_no' => $lastMMa + 1,
                'uid' => UuidV4::uuid4()->toString(),
                'session' => $student->polytechnic_session,
                'student_id' => $student->id,
                'student_name' => $student->name,
                'amount' => $student->fees->sum('amount'),
                'invoice_month' => $request->invoice_month,
                'semester' => $request->semester,
                'invoice_date' => today()->toDateString(),
                'bank_name' => $student->bank_name,
                'bank_branch' => $student->bank_branch,
                'bank_account' => $student->bank_account,
                'fee_type' => json_encode($feeType),
                'created_by' => auth()->user()->id
            ]);
            $details = $invoice->details()->saveMany($invoiceDetails);
        }
        return redirect()->route('invoice.index')->withSuccess("Invoice Generated Successfully.");
    }

    /**
     * Display the specified resource.
     *
     * @param $invoice_id
     * @return \Illuminate\Http\Response
     */
    public function show($invoice_id)
    {
        $this->authorize('view_invoice');
        $invoice = Invoice::query()
            ->where('invoice_id', $invoice_id)
            ->with('details')
            ->with('student')
            ->whereHas('details')
            ->get();
        $basicInfo = $invoice->first();
        $lastMma = 0;
        $feeTypes = $invoice->whereNotNull('details')->first()->details->pluck('fee_type');

        if (in_array('MMA', $feeTypes->toArray())) {
            $lastMma = lastMmaNo($basicInfo->session, $basicInfo->semester);
        }

        return Inertia::render('Invoice/Invoice', [
            'can' => [
                'create' => auth()->user()->can('create_invoice'),
                'update' => auth()->user()->can('update_invoice'),
                'delete' => auth()->user()->can('delete_invoice'),
                'view' => auth()->user()->can('view_invoice'),
            ],
            'data' => $invoice->sortBy('bank_branch')->values(),
            'feeTypes' => $feeTypes,
            'basicInfo' => $basicInfo,
            'last_mma' => $lastMma
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $invoice_id
     * @return \Illuminate\Http\Response
     */
    public function edit($invoice_id)
    {
        $this->authorize('update_invoice');
        $invoice = Invoice::query()
            ->where('invoice_id', $invoice_id)
            ->with('details')
            ->with('student')
            ->get();
        $basicInfo = $invoice->first();
        $feeTypes = $invoice->whereNotNull('details')->first()->details->pluck('fee_type');
        return Inertia::render('Invoice/Edit', [
            'can' => [
                'create' => auth()->user()->can('create_invoice'),
                'update' => auth()->user()->can('update_invoice'),
                'delete' => auth()->user()->can('delete_invoice'),
                'view' => auth()->user()->can('view_invoice'),
            ],
            'data' => $invoice,
            'feeTypes' => $feeTypes,
            'basicInfo' => $basicInfo
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $invoice_id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$invoiceDetails_id)
    {
        $this->authorize('update_invoice');
        $invoiceDetails = InvoiceDetail::find($invoiceDetails_id);
        $invoice = Invoice::find($invoiceDetails->invoice_id);
        $currentTotalAmount = ($invoice->amount - $invoiceDetails->amount) + (int)$request->amount;
        $invoiceDetails->update($request->only('amount', 'student_amount', 'board_amount', 'institute_amount'));
        $invoice->update(['amount' => $currentTotalAmount]);
        $url = route('invoice.edit', $invoice->invoice_id). '#updateForm'.$invoiceDetails_id;
        return redirect($url)->withSuccess("Invoice Successfully Updated.");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $invoice_id
     * @return \Illuminate\Http\Response
     */
    public function destroy($invoice_id)
    {
        $this->authorize('delete_invoice');
        $invoice = Invoice::where('invoice_id',$invoice_id);
        $invoice_details = InvoiceDetail::where('invoice', $invoice_id);
        $invoice_details->delete();
        $invoice->delete();
        return redirect()->route('invoice.index')->withSuccess("Invoice Deleted.");
    }
}
