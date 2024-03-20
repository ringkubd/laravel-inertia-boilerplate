<?php

namespace App\Http\Controllers;

use App\Http\Resources\PlacementResource;
use App\Models\AcademicSession;
use App\Models\Placement;
use App\Models\Student;
use Illuminate\Http\Request;
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
        $placements = PlacementResource::collection(Placement::paginate(20));
        return Inertia::render('Placement/Index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(Request $request): Response
    {
        $academic_sessions = AcademicSession::query()->pluck('session')->toArray();
        $students = Student::with('users')
            ->when($request->search, function ($q, $v){
                $q->where('name', 'like', "%$v%")->orWhere('mobile', 'like', "%$v%");
            })
            ->when($request->academic_session, function ($q, $v){
                $q->where('current_session', 'like', "%$v%");
            })
            ->get();

        return Inertia::render('Placement/Create', [
            'academic_sessions' => $academic_sessions,
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
        //
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
