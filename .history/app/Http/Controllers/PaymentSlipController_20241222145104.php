<?php

namespace App\Http\Controllers;

use App\Http\Resources\PaymentSlipBasicResource;
use App\Models\AcademicSession;
use App\Models\ClassRoom;
use App\Models\PaymentSlip;
use App\Models\Student;
use App\Models\Trade;
use App\Notifications\PaymentSlipNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB; // Add this import
use Illuminate\Support\Facades\Storage; // Add this import
use Illuminate\Support\Str; // Add this import
use Illuminate\Support\Facades\Validator; // Add this import
use Illuminate\Validation\Rule; // Add this import
use Inertia\Inertia;
use Inertia\Response;
use ZipArchive;

class PaymentSlipController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        $paymentSlip = $this->getCollection($request);
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
     * @return \Inertia\Response
     */
    public function create(): Response
    {
        $academic_sessions = AcademicSession::all();
        return Inertia::render('PaymentSlip/Create', [
            'academic_sessions' => $academic_sessions,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'semester' => 'required',
            'fee_type' => ['required', Rule::unique('payment_slips')->using(function ($q) use ($request) {
                $q
                    ->where('student_id', $request->student_id)
                    ->where('fee_type', $request->fee_type)
                    ->where('status', '!=', 2)
                    ->where('semester', $request->semester);
            })],
            'amount' => 'required',
            'attachment' => 'required',
        ]);
        $validator->validate();

        try {
            DB::beginTransaction();
            $result_request = $request->only('student_id', 'semester', 'amount', 'fee_type');
            $result_request['added_by'] = auth()->user()->id;
            $result_request['student_id'] =  $request->student_id;
            $slip = PaymentSlip::create($result_request);

            $image = $request->file('attachment');
            dd($image);
            $extension = getImageMimeType($image);
            $safeName = Str::random(50) . '.' . $extension;
            $store = Storage::disk('public_path')->put("payment_slip/" . $safeName, $image);
            $slip->attachments()->insert([
                'path' => 'payment_slip/' . $safeName,
                'extention' => $extension,
                'payment_slip_id' => $slip->id
            ]);
            DB::commit();
        } catch (\Exception $e) {
            dd($e->getMessage());
            DB::rollBack();
        }
        return redirect()->route('payment-slip.index')->with('Status successfully created');
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
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(PaymentSlip $paymentSlip)
    {
        $paymentSlip->delete();
        return redirect()->route('payment-slip.index')->with('Status successfully deleted');
    }


    public function changeStatus(PaymentSlip $slip, $status)
    {
        $slip->updateOrFail(['status' => $status]);

        $paymentSlip = PaymentSlip::with('student')->find($slip->id);
        $paymentSlip->student->users->notify(new PaymentSlipNotification($paymentSlip));
        return redirect()->back()->with('Status successfully updated');
    }

    public function download()
    {
        dd(123);
    }

    public function downloadAll(Request $request)
    {
        $slips = PaymentSlip::query()
            ->with(['student'])
            ->when($request->current_session, function ($q, $v) use ($request) {
                $q->whereHas('student', function ($q) use ($request) {
                    $q->where('polytechnic_session', $request->current_session);
                });
            })
            ->when($request->semester, function ($q, $v) {
                $q->where('semester', $v);
            })
            ->when($request->search, function ($q, $v) {
                $q->whereHas('student', function ($q) use ($v) {
                    $q->where('name', 'like', "%$v%");
                });
                $q->orWhere('semester', $v);
            })

            ->has('student')
            ->with('attachments')
            ->orderBy('created_at')
            ->where('status', 1)
            ->get();
        if ($slips->count() > 0) {
            $zip = new ZipArchive();
            $rand = rand(9999, 111111);
            $archiveName = "payment_slip/zip/{$rand}.zip";
            if ($zip->open(public_path($archiveName), ZipArchive::CREATE) === true) {
                foreach ($slips as $slip) {
                    $fee_type = $slip->fee_type;
                    $student_name = $slip->student->name;
                    foreach ($slip->attachments as $attach) {
                        $file = public_path($attach->path);
                        $fileName = str_replace(" ", "_", $fee_type) . "_" . str_replace(" ", "_", $student_name) . "_" . $attach->id . "." . $attach->extention;
                        if (File::exists($file)) {
                            $zip->addFile($file, $fileName);
                        }
                    }
                }
                $zip->close();
                return response()->download(public_path($archiveName));
            }
        }
        return back();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getCollection(Request $request): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return PaymentSlip::query()
            ->with(['student'])
            ->when($request->current_session, function ($q, $v) use ($request) {
                $q->whereHas('student', function ($q) use ($request) {
                    $q->where('polytechnic_session', $request->current_session);
                });
            })
            ->when($request->semester, function ($q, $v) {
                $q->where('semester', $v);
            })
            ->when($request->search, function ($q, $v) {
                $q->whereHas('student', function ($q) use ($v) {
                    $q->where('name', 'like', "%$v%");
                });
                $q->orWhere('semester', $v);
            })

            ->has('student')
            ->with('attachments')
            ->orderBy('created_at')
            ->paginate();
    }

    /**
     * Summary of getSetudents
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function getSetudents(Request $request)
    {
        $students =  Student::query()
            ->whereNotNull('polytechnic_id')
            ->where('polytechnic_session', $request->academic_session)
            ->get();
        return response()->json($students);
    }
}
