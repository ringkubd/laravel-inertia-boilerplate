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
use Illuminate\Support\Facades\Log;
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
        $attachment = $request->file('attachment');
        $uploadDebug = [
            'has_attachment_key' => $request->has('attachment'),
            'has_file_attachment' => $request->hasFile('attachment'),
            'php_upload_max_filesize' => ini_get('upload_max_filesize'),
            'php_post_max_size' => ini_get('post_max_size'),
            'php_memory_limit' => ini_get('memory_limit'),
            'content_length' => $request->server('CONTENT_LENGTH'),
            'content_type' => $request->server('CONTENT_TYPE'),
        ];

        if ($attachment) {
            $uploadDebug = array_merge($uploadDebug, [
                'original_name' => $attachment->getClientOriginalName(),
                'client_mime' => $attachment->getClientMimeType(),
                'file_size_bytes' => $attachment->getSize(),
                'error_code' => $attachment->getError(),
                'error_message' => $attachment->getErrorMessage(),
                'is_valid' => $attachment->isValid(),
            ]);
        }

        if ($attachment && !$attachment->isValid()) {
            $uploadErrors = [
                UPLOAD_ERR_INI_SIZE => 'Attachment is too large for server upload limit (upload_max_filesize).',
                UPLOAD_ERR_FORM_SIZE => 'Attachment is too large for form upload limit.',
                UPLOAD_ERR_PARTIAL => 'Attachment was only partially uploaded. Please try again.',
                UPLOAD_ERR_NO_TMP_DIR => 'Server temporary upload directory is missing.',
                UPLOAD_ERR_CANT_WRITE => 'Server failed to write uploaded file.',
                UPLOAD_ERR_EXTENSION => 'Upload blocked by a PHP extension.',
            ];

            Log::warning('Payment slip attachment upload is invalid before validation', $uploadDebug);

            $errors = [
                'attachment' => $uploadErrors[$attachment->getError()] ?? 'Attachment upload failed. Please try again.',
            ];

            if (app()->environment('local')) {
                $errors['attachment_debug'] = 'Debug: ' . json_encode($uploadDebug);
            }

            return redirect()
                ->back()
                ->withInput()
                ->withErrors($errors);
        }

        $validator = Validator::make(array_merge($request->all(), $request->allFiles()), [
            'student_id' => ['required', 'exists:students,id'],
            'semester' => ['required', Rule::unique('payment_slips')->where(function ($query) use ($request) {
                return $query->where('student_id', $request->student_id)
                    ->where('fee_type', $request->fee_type)
                    ->where('status', '!=', 2)
                    ->where('semester', $request->semester);
            })],
            'amount' => 'required',
            'fee_type' => 'required',
            'attachment' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        if ($validator->fails()) {
            if ($validator->errors()->has('attachment')) {
                Log::warning('Payment slip attachment validation failed', array_merge($uploadDebug, [
                    'validation_errors' => $validator->errors()->get('attachment'),
                ]));
            }

            return redirect()
                ->back()
                ->withInput()
                ->withErrors($validator);
        }

        try {
            DB::beginTransaction();

            $result_request = $validator->validated();
            $result_request['added_by'] = auth()->user()->id;

            $slip = PaymentSlip::create($result_request);

            $image = $request->file('attachment');
            $extension = pathinfo($image->getClientOriginalName(), PATHINFO_EXTENSION);
            $safeName = Str::random(50) . '.' . $extension;

            if (!Storage::disk('public_path')->put("payment_slip/" . $safeName, file_get_contents($image))) {
                throw new \Exception('File upload failed');
            }

            $slip->attachments()->create([
                'path' => 'payment_slip/' . $safeName,
                'extention' => $extension,
                'payment_slip_id' => $slip->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            DB::commit();

            return redirect()
                ->route('payment-slip.index')
                ->with('success', 'Payment slip created successfully');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Payment slip store failed', array_merge($uploadDebug, [
                'exception_message' => $e->getMessage(),
            ]));

            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create payment slip: ' . $e->getMessage()]);
        }
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
        foreach ($paymentSlip->attachments as $attach) {
            $file = public_path($attach->path);
            if (file_exists($file)) {
                unlink($file);
            }
            $attach->delete();
        }
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
            ->orderBy('created_at', 'desc')
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
