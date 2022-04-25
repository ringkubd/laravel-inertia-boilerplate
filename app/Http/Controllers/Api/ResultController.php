<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateResultRequest;
use App\Http\Resources\Api\ResultResource;
use App\Models\ClassRoom;
use App\Models\Result;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ResultController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();
        $result = Result::where('student_id', $user?->student?->id)
        ->orderBy('semester')->get();
        return response()->json([
            'success' => true,
            'data' =>new ResultResource($result),
        ], 201);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreResultRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $messages = [
            'semester.unique' => 'Given Semester Result Already Exists and Passed.',
        ];

        $semester = $request->semester;
        $student_id = $request->student_id;
        $validate = Validator::make($request->all(), [
            'semester' => [
                'required', Rule::unique('results')->using(function ($query) use($student_id) {
                    return $query->where('student_id', $student_id)
                        ->whereNull('deleted_at')
                        ->where('status', '!=',"Referred");
                }),
                'max:8',
                'min:1',
            ],
            'student_id' => ['required'],
            'status' => ['required'],
            'failed_in_subject' => 'required',
            'attachment' => 'required'
        ],
            $messages
        );
        if ($validate->fails()) {
            return [
                'status' => false,
                'messages' => $validate->errors(),
                'data' => []
            ];
        }

        $existingResult =  Result::where('student_id', $request->student_id)
            ->where('semester', $request->semester)
            ->where('status', '=', 'Referred')->count();

        if ($request->status !== "Dropout" && $request->semester != 8 && $existingResult == 0) {
            $classRoom = ClassRoom::where('class_name_number', $request->semester + 1)->first();
            $student = Student::find($student_id);
            $student->update(['semester' => $classRoom->class_name_number]);
            $student->classroom()->sync($classRoom->id);
        }
        if ($request->semester == 8 && $request->status == "Passed") {
            $student = Student::find($student_id);
            $student->update(['polytechnic_completed' => 1]);
        }
        try {
            DB::beginTransaction();
            $result_request = $request->all();
            $result_request['added_by'] = auth()->user()->id;
            $result = Result::create($result_request);

            $image = base64_decode($request->attachment);
            $safeName = Str::random(10).'.'.'png';
            $store = Storage::disk('public_path')->put("result_document/".$safeName, $image);
            $result->attachments()->insert([
                'attachment' => 'result_document/'.$safeName,
                'result_id' => $result->id
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
        return response()->json([
            'success' => true,
            'data' =>new ResultResource($result),
        ], 201);


    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Result  $result
     * @return \Illuminate\Http\Response
     */
    public function show(Result $result)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateResultRequest  $request
     * @param  \App\Models\Result  $result
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateResultRequest $request, Result $result)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Result  $result
     * @return \Illuminate\Http\Response
     */
    public function destroy(Result $result)
    {
        //
    }

    private function saveBase64Image($image_64){
        $extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1];   // .jpg .png .pdf
        $replace = substr($image_64, 0, strpos($image_64, ',')+1);
        $image = str_replace($replace, '', $image_64);
        return $image;

        $image = str_replace(' ', '+', $image);

        $imageName = Str::random(10).'.'.$extension;
        $file = now().'.'.$imageName;
        Storage::disk('public')->put($file, base64_decode($image));
        return $file;
    }
}
