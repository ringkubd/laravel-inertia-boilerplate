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
        $invoices = Invoice::query()
            ->select('invoice_id', 'invoice_month', 'invoice_date', DB::raw( 'sum(amount) as total_amount'))
            ->when($request->search, function ($q, $v){
                $q->where('student_id', 'like', "%$v%")
                    ->orWhere('invoice_month', 'like', "%$v%")
                    ->orWhere('invoice_id', 'like', "%$v%")
                    ->orWhere('invoice_date', 'like', "%$v%")
                    ->orWhere('student_name', 'like', "%$v%");
            })
            ->orderBy('invoice_id')
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
        $sessions = AcademicSession::all();
        $students = Student::query()
            ->with(['fees' => function($q) use ($request){
                $q->where('semester', $request->semester);
            }])
            ->whereHas('fees', function ($q) use ($request){
                $q->where('semester', $request->semester);
            })
            ->whereHas('results', function($q) use($request){
                $q->where('status','!=','Dropout')
                    ->where('semester', $request->semester);
            })
            ->where('polytechnic_session', "$request->polytechnic_session")
            ->get();
        $feeTypes = $students->whereNotNull('fees')->max('fees');
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
        $students = Student::query()
            ->with(['fees' => function($q) use ($request, $billableFee){
                $q->where('semester', $request->semester)
                ->whereIn('fee_type', array_keys($billableFee));
            }])
            ->whereHas('fees', function ($q) use ($request){
                $q->where('semester', $request->semester);
            })
            ->whereHas('results', function($q) use($request){
                $q->where('status','!=','Dropout')
                    ->where('semester', $request->semester);
            })
            ->where('polytechnic_session', "$request->academic_session")
            ->get();

        $invoiceId = rand(1111,99999);
        foreach($students as $student){
            $invoiceDetails = [];
            foreach ($student->fees as $fee){
                $invoiceDetails[] = new InvoiceDetail([
                    'fee_type' => $fee->fee_type,
                    'amount' => $fee->amount
                ]);
            }

            $invoice = Invoice::create([
                'invoice_id' => $invoiceId,
                'student_id' => $student->id,
                'student_name' => $student->name,
                'amount' => $student->fees->sum('amount'),
                'invoice_month' => $request->invoice_month,
                'semester' => $request->semester,
                'invoice_date' => today()->toDateString(),
                'bank_name' => $student->bank_name,
                'bank_branch' => $student->bank_branch,
                'bank_account' => $student->bank_account,
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
        $invoice = Invoice::query()
            ->where('invoice_id', $invoice_id)
            ->with('details')
            ->with('student')
            ->get();
        $basicInfo = $invoice->first();
        $feeTypes = $invoice->whereNotNull('details')->first()->details->pluck('fee_type');
        return Inertia::render('Invoice/Invoice', [
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
     * Show the form for editing the specified resource.
     *
     * @param $invoice_id
     * @return \Illuminate\Http\Response
     */
    public function edit($invoice_id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $invoice_id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$invoice_id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $invoice_id
     * @return \Illuminate\Http\Response
     */
    public function destroy($invoice_id)
    {
        //
    }
}
