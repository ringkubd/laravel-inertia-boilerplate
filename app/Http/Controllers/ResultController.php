<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Models\ClassRoom;
use App\Models\Result;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class ResultController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view_polytechnic_result');
        $result = Student::query()
            ->polytechnic()
            ->with('results.details', 'results.attachments', 'polytechnic')
            ->when($request->academic_session, function ($q, $v) {
                $q->where('polytechnic_session', $v);
            })
            ->when($request->search, function ($q, $v) {
                $q->where(function ($q) use ($v){
                    $q->where('polytechnic_session', 'like', "%$v%")
                        ->orWhere('name', 'like', "%$v%")
                        ->orWhere('name', 'like', "%$v%");
                });
            })
            ->when(auth()->user()->hasRole('Student'), function ($q){
                $q->where('users_id', auth()->user()->id);
            })
            ->paginate(10);
        $sessions = AcademicSession::query()
            ->get();

        return Inertia::render('Result/Index', [
            'data' => $result,
            'can' => [
                'create' => auth()->user()->can('create_result'),
                'update' => auth()->user()->can('update_result'),
                'delete' => auth()->user()->can('delete_result'),
                'view' => auth()->user()->can('view_result'),
            ],
            'sessions' => $sessions
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create_polytechnic_result');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     *
     */
    public function store(Request $request)
    {
        $this->authorize('create_polytechnic_result');
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
            'supporting_document' => 'required'
        ],
            $messages
        );
        if ($validate->fails()) {
            return redirect()->back()->withErrors($validate->errors());
        }
        $existingResult =  Result::where('student_id', $request->student_id)
            ->where('semester', $request->semester)
            ->where('status', '=', 'Referred')->count();

        if ($request->status !== "Dropout" && $request->semester != 8 && $existingResult == 0) {
            $classRoom = ClassRoom::where('class_name_number', $request->semester + 1)->first();
            $student = Student::find($student_id);
            $student->classroom()->sync($classRoom->id);
        }
        if ($request->semester == 8 && $request->status == "Passed") {
            $student = Student::find($student_id);
            $student->update(['polytechnic_completed' => 1]);
        }

        $fileName = [];
        $result_request = $request->all();
        $result_request['added_by'] = auth()->user()->id;
        $result = Result::create($result_request);
        if ($request->hasFile('supporting_document')) {
            $documents = $request->file('supporting_document');
            foreach ($documents as $doc){
                $file = now().'.'.$doc->getClientOriginalExtension();
                $doc->move(public_path("result_document"), $file);
                $fileName[] = [
                    'attachment' => "result_document/{$file}",
                    'result_id' => $result->id
                ];
            }
        }
        $result->attachments()->insert($fileName);
        return redirect()->back()->withSuccess("Result Successfully Added.");

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->authorize('view_polytechnic_result');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
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
    public function destroy(Result $result)
    {
        $this->authorize('delete_polytechnic_result');
        $result->delete();
        return redirect()->back()->withSuccess("Successfully deleted.");
    }

    /**
     *
     */
    public function individualResult(){

    }

    /**
     * Student List
     */

    public function studentList(Request $request){
        $students = Student::query()
            ->when($request->name, function ($q, $v) {
                $q->where('name', 'like', "%$v%");
            })
            ->when($request->academic_session, function ($q, $v) {
                $q->where('polytechnic_session', 'like', "%$v%");
            })
            ->select('name as label', 'id as value')
            ->polytechnic();
        if (auth()->user()->hasRole('Student')) {
            $students->where('users_id', auth()->user()->id);
        }
        return $students->get();

    }
}
