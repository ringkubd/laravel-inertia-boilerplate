<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Models\ClassRoom;
use App\Models\PaymentSlip;
use App\Models\Trade;
use App\Notifications\PaymentSlipNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Inertia\Inertia;
use ZipArchive;

class PaymentSlipController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $paymentSlip = $this->getCollection($request);

        $classes =  ClassRoom::where('is_madrasa', false)->get();
        $session = AcademicSession::all();
        $trade = Trade::where('is_madrasa', 0)->get();

        return Inertia::render('PaymentSlip/Index', [
            'payment_slip' => $paymentSlip,
            'trades' => $trade,
            'academic_session' => $session,
            'can' => [
                'create' => auth()->user()->can('create_payment-slip'),
                'update' => auth()->user()->can('update_payment-slip'),
                'delete' => auth()->user()->can('create_payment-slip'),
                'view' => auth()->user()->can('update_payment-slip'),
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PaymentSlip  $paymentSlip
     * @return \Illuminate\Http\Response
     */
    public function show(PaymentSlip $paymentSlip)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PaymentSlip  $paymentSlip
     * @return \Illuminate\Http\Response
     */
    public function edit(PaymentSlip $paymentSlip)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PaymentSlip  $paymentSlip
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PaymentSlip $paymentSlip)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PaymentSlip  $paymentSlip
     * @return \Illuminate\Http\Response
     */
    public function destroy(PaymentSlip $paymentSlip)
    {
        $paymentSlip->delete();
        return redirect()->route('payment-slip.index')->with('Status successfully deleted');
    }


    public function changeStatus(PaymentSlip $slip, $status){
        $slip->updateOrFail(['status' => $status]);

        $paymentSlip = PaymentSlip::with('student')->find($slip->id);
        $paymentSlip->student->users->notify(new PaymentSlipNotification($paymentSlip));
        return redirect()->route('payment-slip.index')->with('Status successfully updated');
    }

    public function download(){
        dd(123);
    }

    public function downloadAll(Request $request){
        $slips = $this->getCollection($request)->where('status', 1);
        if ($slips->count() > 0){
            $zip = new ZipArchive();
            $archiveName = "payment_slip/zip/45.zip";
            if ($zip->open(public_path($archiveName), ZipArchive::CREATE) === true){
                foreach ($slips as $slip){
                    $fee_type = $slip->fee_type;
                    $student_name = $slip->student->name;
                    foreach ($slip->attachments as $attach){
                        $file = public_path($attach->path);
                        $fileName = str_replace(" ","_" ,$fee_type)."_".str_replace(" ", "_", $student_name)."_".$attach->id.".".$attach->extention;
                        if (File::exists($file)){
                            $zip->addFile($file, $fileName);
                        }
                    }
                }
                $zip->close();
                return response()->download(public_path($archiveName));
            }

        }
    }

    /**
     * @param Request $request
     * @return array|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getCollection(Request $request)
    {
        return PaymentSlip::query()
            ->with(['student'])
            ->when($request->academic_session, function ($q, $v) use ($request) {
                $q->whereHas('student', function ($q) use ($request) {
                    $q->where('polytechnic_session', $request->academic_session);
                });
            })
            ->when($request->semester, function ($q, $v) {
                $q->where('semester', $v);
            })
            ->when($request->fee_type, function ($q, $v) {
                $q->where('fee_type', $v);
            })
            ->when($request->search, function ($q, $v) {
                $q->whereHas('student', function ($q) use ($v) {
                    $q->where('name', 'like', "%$v%");
                });
                $q->orWhere('semester', $v);
                $q->orWhere('semester', $v);
            })
            ->when($request->current_session, function ($q, $v) {
                $q->whereHas('student', function ($q) use ($v) {
                    $q->where('current_session', 'like', "%$v%");
                });
            })
            ->when($request->trade, function ($q, $v) {
                $q->whereHas('student', function ($q) use ($v) {
                    $q->where('madrasa_trade_id', 'like', "%$v%");
                });
            })
            ->has('student')
            ->with('attachments')
            ->orderBy('created_at')
            ->get();
    }
}
