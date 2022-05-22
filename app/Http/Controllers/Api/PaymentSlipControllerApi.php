<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\PaymentSlipResource;
use App\Models\FeeType;
use App\Models\PaymentSlip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PaymentSlipControllerApi extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($student=false, $semester = false)
    {
        $paymentSlip = PaymentSlip::query()
            ->when($student, function ($q, $v){
                $q->where('student_id', $v);
            })->when($semester, function ($q, $v){
                $q->where('semester', $v);
            })
            ->with('attachments')
            ->get();

        return response()->json(new PaymentSlipResource($paymentSlip));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required',
            'semester' => 'required',
            'fee_type' => ['required', Rule::unique('payment_slips')->using(function ($q) use($request){
                $q
                    ->where('student_id', $request->student_id)
                    ->where('fee_type', $request->fee_type)
                    ->where('status','!=',2)
                    ->where('semester', $request->semester);
            })],
            'amount' => 'required',
            'attachment' => 'required',
            'extention' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->messages(), 403);
        }
        try {
            DB::beginTransaction();
            $result_request = $request->only('student_id', 'semester','amount', 'fee_type');
            $result_request['added_by'] = auth()->user()->id;
            $slip = PaymentSlip::create($result_request);

            $image = base64_decode($request->attachment);
            $safeName = Str::random(50).'.'.$request->extention;
            $store = Storage::disk('public_path')->put("payment_slip/".$safeName, $image);
            $slip->attachments()->insert([
                'path' => 'payment_slip/'.$safeName,
                'extention' => $request->extention,
                'payment_slip_id' => $slip->id
            ]);
            DB::commit();

        }catch (\Exception $e){
            DB::rollBack();
            return [
                'status' => false,
                'messages' => [
                    "error" => $e->getMessage()
                ],
                'data' => []
            ];
        }

        return new PaymentSlipResource($slip);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $slip = PaymentSlip::find($id);
            Storage::disk('public_path')->delete($slip->attachments->pluck('path'));
            $slip->attachments->delete();
            $slip->delete();
        }catch (\Exception $e){
            return [
                'status' => false,
                'messages' => [
                    "error" => $e->getMessage()
                ],
                'data' => []
            ];
        }
        return [
            'status' => true,
            'data' => []
        ];


    }

    public function getFeeType(){
        $feeType = FeeType::where('is_madrasa', 0)->selectRaw('name as value, name as label')->get()->toArray();
        return response()->json($feeType);
    }
}
