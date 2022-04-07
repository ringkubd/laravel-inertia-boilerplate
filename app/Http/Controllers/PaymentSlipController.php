<?php

namespace App\Http\Controllers;

use App\Models\PaymentSlip;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PaymentSlipController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $paymentSlip = PaymentSlip::query()
            ->with('student', 'attachments')
            ->orderBy('created_at')
            ->get();
        return Inertia::render('PaymentSlip/Index', [
            'payment_slip' => $paymentSlip,
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
}
