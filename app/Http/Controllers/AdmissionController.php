<?php

namespace App\Http\Controllers;

use App\Imports\AdmissionResultUpdate;
use App\Models\AcademicSession;
use App\Models\Admission;
use App\Models\Polytechnic;
use App\Models\Student;
use App\Models\Trade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use MeiliSearch\Exceptions\ApiException;

class AdmissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->has('search') && $request->search != "") {
            try {
                $admissions = Admission::search($request->search)->paginate();
            } catch (ApiException $exception){
                abort(404);
            }
        }else{
            $admissions = Admission::query()->paginate();
        }


        $academicSession = AcademicSession::selectRaw('session as text')->get();

        return Inertia::render('Admission/Index', [
            'admissions' => $admissions,
            'academicSession' => $academicSession,
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
            ->whereNull('polytechnic_id')
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
        $request->validate([
            'student_id' => '',
            'academic_session' => '',
            'tracking_id' => '',
        ], [
            'student_id' => "Please choose a student."
        ]);
        $admission = Admission::create([
            'student_id' => $request->student_id,
            'academic_session' => $request->academic_session,
            'tracking_id' => $request->tracking_id,
            'created_by' => auth()->user()->name,
        ]);
        if ($request->trades) {
            $admission->trade()->attach($request->trades);
        }
        if ($request->polytechnics) {
            $admission->polytechnic()->attach($request->trades);
        }
        if ($request->hasFile('supporting_documents')) {
            $supporting_document = $request->file('supporting_documents');
            $directory = public_path('admission_attach/supporting_documents');
            $file_name = $supporting_document->getClientOriginalName().'_'.rand(9999, 9999999999).'.'.$supporting_document->getClientOriginalExtension();
            $supporting_document->move($directory, $file_name);
            $admission->update([
                'supporting_documents' => 'admission_attach/supporting_documents/'.$file_name
            ]);
        }
        return \redirect()->route('admission.index')->withSuccess("Successfully added.");
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
            ->whereNull('polytechnic_id')
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

    /**
     * Read CSV
     */

    public function updateAdmission(){
        $extractCSV = Excel::import(new AdmissionResultUpdate(),public_path('addresses.csv'));
        dd($extractCSV);
    }
}
