<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Rules\AcademicSessionValidation;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AcademicSessionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view_academic_session');
        $data = AcademicSession::query()
            ->when($request->search, function ($q, $v) {
                $q->where('session', 'like', "%$v%");
            })
            ->paginate();
        return Inertia::render('AcademicSession/Index', [
            'data' => $data,
            'can' => [
                'create' => auth()->user()->hasrole('Super Admin'),
                'update' => auth()->user()->hasrole('Super Admin'),
                'delete' => auth()->user()->hasrole('Super Admin'),
                'view' => auth()->user()->hasrole('Super Admin'),
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
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('create_academic_session');
        $request->validate([
            'academic_session' => ['required', new AcademicSessionValidation, 'unique:academic_sessions,session'],
            'start_date' => ['required']
        ]);
        $data = AcademicSession::create([
            'session' => $request->input('academic_session'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
        ]);
        return redirect()->route('academic_session.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->authorize('update_academic_session');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->authorize('update_academic_session');
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
        $this->authorize('delete_academic_session');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->authorize('delete_academic_session');
        $academic_session = AcademicSession::find($id)->delete();
        return redirect()->route('academic_session.index');
    }
}
