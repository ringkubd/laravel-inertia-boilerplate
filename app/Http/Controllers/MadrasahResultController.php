<?php

namespace App\Http\Controllers;

use App\Models\MadrasahResult;
use App\Models\Result;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class MadrasahResultController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view_madrasa_result');
        $result = MadrasahResult::when($request->academic_session, function ($q, $v) {
            $q->whereHas('student', function ($q) use($v) {
                $q->madrasa()->whereHas('madrashaSession', function ($q) use($v) {
                    $q->where('session', $v);
                });
            });
        })->has('student')->paginate();
        return Inertia::render('Madrasa/Result/Index', [
            'results' => $result,
            'can' => [
                'create' => auth()->user()->can('create_madrasa_result'),
                'update' => auth()->user()->can('update_madrasa_result'),
                'delete' => auth()->user()->can('delete_madrasa_result'),
                'view' => auth()->user()->can('view_madrasa_result'),
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create_madrasa_result');
        $students = Student::query()->madrasa()->whereHas('madrashaSession', function($q){
            $q->whereNull('end_date');
        })->get();

        return Inertia::render('Madrasa/Result/Create', [
            'result' => new MadrasahResult(),
            'can' => [
                'create' => auth()->user()->can('create_madrasa_result'),
                'update' => auth()->user()->can('update_madrasa_result'),
                'delete' => auth()->user()->can('delete_madrasa_result'),
                'view' => auth()->user()->can('view_madrasa_result'),
            ],
            'students' => $students
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
        $this->authorize('create_madrasa_result');
        $request->validate([
            'student_id' => ['required','unique:madrasah_results,student_id'],
            'nine_gpa' => 'required_without:ten_gpa',
            'ten_gpa' => 'required_without:nine_gpa',
            'pass_year' => 'required_with:ten_gpa',
            'status' => 'required'
        ], [
            'student_id.required' => 'Please select a student. Student field is required.',
            'student_id.unique' => 'You already added this student results..',
            'ten_gpa.required_without' => 'The Dakhil CGPA is required when Nine GPA is not present...',
            'nine_gpa.required_without' => 'The Nine GPA is required...',
        ]);
        $result_request = $request->all();
        $result_request['added_by'] = auth()->user()->id;
        $result = MadrasahResult::create($result_request);
        if ($request->has('ten_gpa') && $request->ten_gpa != "" && $request->status == "Pass") {
            $result->student()->update(['madrasa_completed' => true]);
        }
        return redirect()->route('madrasa.result.index')->withSuccess("Result updated.");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->authorize('view_madrasa_result');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->authorize('update_madrasa_result');
        return Inertia::render('Madrasa/Result/Edit', [
            'result' => MadrasahResult::find($id),
            'can' => [
                'create' => auth()->user()->can('create_madrasa_result'),
                'update' => auth()->user()->can('update_madrasa_result'),
                'delete' => auth()->user()->can('delete_madrasa_result'),
                'view' => auth()->user()->can('view_madrasa_result'),
            ]
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MadrasahResult $result)
    {
        $this->authorize('update_madrasa_result');
        $request->validate([
            'nine_gpa' => 'required_without:ten_gpa',
            'ten_gpa' => 'required_without:nine_gpa',
            'pass_year' => 'required_with:ten_gpa',
            'status' => 'required'
        ], [
            'ten_gpa.required_without' => 'The Dakhil CGPA is required when Nine GPA is not present...',
            'nine_gpa.required_without' => 'The Nine GPA is required...',
        ]);

        $result->update($request->all());
        if ($request->ten_gpa != "" && $request->status == 'Pass') {
            $result->student()->update(['madrasa_completed' => true]);
        }
        return redirect()->route('madrasa.result.index')->withSuccess("Result updated.");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(MadrasahResult $result)
    {
        $this->authorize('delete_madrasa_result');
        $result->delete();
        return redirect()->route('madrasa.result.index')->withSuccess("Result deleted.");
    }
    /**
     * Student List
     */

    public function studentList(Request $request){
        return Student::query()
            ->when($request->name, function ($q, $v) {
                $q->where('name', 'like', "%$v%");
            })
            ->when($request->academic_session, function ($q, $v) {
                $q->where('ssc_session', 'like', "%$v%");
            })
            ->select('name as label', 'id as value')
            ->madrasa()
            ->doesntHave('madrasahResult')
            ->get();
    }
}
