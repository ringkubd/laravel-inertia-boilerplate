<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Models\Admission;
use App\Models\Polytechnic;
use App\Models\Student;
use App\Models\Trade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use function React\Promise\all;

class AdmissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $admissions = Admission::query()
            ->when($request->search, function($q, $v){
                $q->whereHas('student', function ($q) use($v){
                    $q->where('name', 'like', "%$v%")
                        ->orWhereHas('madrasha', function ($q) use($v){
                            $q->where('name', 'like', "%$v%");
                        });
                })
                    ->orWhere('academic_session', 'like', "%$v%")
                    ->orWhere('tracking_id', 'like', "%$v%");
            })
            ->with('student', 'trade', 'polytechnic')
            ->paginate();

        return Inertia::render('Admission/Index', [
            'admissions' => $admissions,
            'can' => [
                'create' => auth()->user()->can('create_admission'),
                'update' => auth()->user()->can('update_admission'),
                'delete' => auth()->user()->can('delete_admission'),
                'view' => auth()->user()->can('view_admission'),
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
        $student = Student::query()
            ->where('madrasa_completed', 1)
            ->whereNull('polytechnic')
            ->whereHas('madrasahResult', function ($q) {
                $q->where('status', 'Pass')
                    ->whereNotNull('ten_gpa')
                    ->whereRaw("STR_TO_DATE(`pass_year`, '%Y') > YEAR(DATE_SUB(CURDATE(), INTERVAL 3 YEAR))");
            })->get();

        $trades = Trade::select('name as label', 'id as value')->where('is_madrasa', 0)->get();
        $sessions = AcademicSession::select('session as label', 'session as value')->get();
        $polytechnic = Polytechnic::select('name as label', 'id as value')->get();

        return Inertia::render('Admission/Create', [
            'students' => $student,
            'trades' => $trades,
            'sessions' => $sessions,
            'polytechnic' => $polytechnic,
            'can' => [
                'create' => auth()->user()->can('create_admission'),
                'update' => auth()->user()->can('update_admission'),
                'delete' => auth()->user()->can('delete_admission'),
                'view' => auth()->user()->can('view_admission'),
            ]
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

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $admission = Admission:: query()
            ->with('student', 'trade', 'polytechnic')
            ->where('id', $id)
            ->first();
        return  Inertia::render('Admission/Details', [
            'admission' => $admission
        ]);
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
    public function destroy(Admission $admission)
    {
        $admission->delete();
        return Redirect::route('admission.index');
    }

    /**
     * Student List
     */

    public function studentList(Request $request){
        $student = Student::query()
            ->when($request->name, function ($q, $v) {
                $q->where('name', 'like', "%$v%");
            })
            ->where('madrasa_completed', 1)
            ->whereNull('polytechnic')
            ->whereHas('madrasahResult', function ($q) {
                $q->where('status', 'Pass')
                    ->whereNotNull('ten_gpa')
                    ->whereRaw("pass_year > YEAR(DATE_SUB(CURDATE(), INTERVAL 3 YEAR))");
            })
            ->select('name as label', 'id as value')
            ->get();

        return $student;
    }

    /**
     * Student Profile
     */

    public function student($student){
        return Student::with('madrasahResult')->find($student);
    }
}
