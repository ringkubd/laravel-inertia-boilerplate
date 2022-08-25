<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Models\ClassRoom;
use App\Models\PaymentSlip;
use App\Models\Trade;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PaymentSlipController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $paymentSlip = PaymentSlip::query()
            ->with(['student'])
            ->when($request->academic_session, function ($q, $v) use($request) {
                $q->whereHas('student', function ($q) use($request){
                    $q->where('polytechnic_session',$request->academic_session);
                });
            })
            ->when($request->semester, function ($q, $v){
                $q->where('semester', $v);
            })
            ->when($request->fee_type, function ($q, $v){
                $q->where('fee_type', $v);
            })
            ->when($request->search, function ($q, $v){
                $q->whereHas('student', function ($q) use($v){
                    $q->where('name','like',"%$v%");
                });
                $q->orWhere('semester', $v);
                $q->orWhere('semester', $v);
            })
            ->when($request->current_session, function ($q, $v){
                $q->whereHas('student', function ($q) use($v){
                    $q->where('current_session', 'like', "%$v%");
                });
            })
            ->when($request->trade, function ($q, $v){
                $q->whereHas('student', function ($q) use($v){
                    $q->where('madrasa_trade_id', 'like', "%$v%");
                });
            })
            ->with('attachments' )
            ->orderBy('created_at')
            ->get();

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
        //
    }


    public function changeStatus(PaymentSlip $slip, $status){
        $slip->updateOrFail(['status' => $status]);
        return redirect()->route('payment-slip.index')->with('Status successfully updated');
    }

    public function download(){

    }
}
