<?php

namespace App\Http\Controllers;

use App\Http\Resources\PlacementResource;
use App\Models\AcademicSession;
use App\Models\Employment;
use App\Models\FurtherEducation;
use App\Models\OtherPlacementStatus;
use App\Models\Placement;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class PlacementStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(): Response
    {
        $placements = Placement::query()
            ->with('status', 'student')
            ->paginate(20);
        return Inertia::render('Placement/Index', compact('placements'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(Request $request): Response
    {
        $academic_sessions = AcademicSession::query()->pluck('session')->toArray();

        return Inertia::render('Placement/Create', [
            'academic_sessions' => $academic_sessions,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'final_result' => 'required',
            'present_status' => ['required',Rule::in(["higher_study", "employment", "other"])],
            'higher_study.institute_name' => 'required_if:present_status,higher_study',
            'higher_study.degree' => 'required_if:present_status,higher_study',
            'other.note' => 'required_if:present_status,other',
            'employment.organization' => 'required_if:present_status,employment',
            'employment.salary' => 'required_if:present_status,employment',
            'employment.position' => 'required_if:present_status,employment',
        ], [
            'higher_study.institute_name.required_if' => 'Institute name is required.',
            'higher_study.degree.required_if' => 'Degree is required.',
            'employment.organization.required_if' => 'Organization name is required.',
            'employment.salary.required_if' => 'Salary is required.',
            'employment.position.required_if' => 'Position is required.',
            'other.note.required_if' => 'Note is required.',
        ]);

        switch ($request->present_status){
            case "higher_study":
                $education = FurtherEducation::create([
                    'student_id' => $request->student_id,
                    'added_by' => $request->user()->id,
                    ...$request->higher_study
                ]);
                $education->placement()->create([
                    'student_id' => $request->student_id,
                    'final_result' => $request->final_result,
                    'added_by' => $request->user()->id,
                ]);
                break;
            case "employment":
                $employment = Employment::create([
                    'student_id' => $request->student_id,
                    'added_by' => $request->user()->id,
                    ...$request->employment
                ]);
                $employment->placement()->create([
                    'student_id' => $request->student_id,
                    'final_result' => $request->final_result,
                    'added_by' => $request->user()->id,
                ]);
                break;
            case "other":
                $employment = OtherPlacementStatus::create([
                    'student_id' => $request->student_id,
                    'added_by' => $request->user()->id,
                    ...$request->other
                ]);
                $employment->placement()->create([
                    'student_id' => $request->student_id,
                    'final_result' => $request->final_result,
                    'added_by' => $request->user()->id,
                ]);
                break;
        }
        return redirect()->back();

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
    public function destroy($id)
    {
        //
    }
}
