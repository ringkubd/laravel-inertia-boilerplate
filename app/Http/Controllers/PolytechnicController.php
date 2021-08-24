<?php

namespace App\Http\Controllers;

use App\Models\Madrasha;
use App\Models\Polytechnic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class PolytechnicController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = Polytechnic::when($request->search, function ($q, $v) {
            $q->where('name', 'like',"%".$v."%");
        })->get();
        return Inertia::render('Polytechnic/Index', [
            'data' => $data,
            'can' => [
                'create' => auth()->user()->can('create_madrasa'),
                'update' => auth()->user()->can('update_madrasa'),
                'delete' => auth()->user()->can('delete_madrasa'),
                'view' => auth()->user()->can('view_madrasa'),
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
        $data = new Polytechnic();
        return Inertia::render('Polytechnic/Create', [
            'data' => $data,
            'can' => [
                'create' => auth()->user()->can('create_madrasa'),
                'update' => auth()->user()->can('update_madrasa'),
                'delete' => auth()->user()->can('delete_madrasa'),
                'view' => auth()->user()->can('view_madrasa'),
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
        $validate = $request->validate([
            'name' => 'required|max:250',
            'address' => 'required|max:250',
            'contact_number' => 'required',
            'institution_number' => 'required',
        ]);
        Polytechnic::create($request->all());
        return Redirect::route('polytechnic.index');
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
        $data = Polytechnic::find($id);
        return Inertia::render('Polytechnic/Edit', [
            'data' => $data,
            'can' => [
                'create' => auth()->user()->can('create_polytechnic'),
                'update' => auth()->user()->can('update_polytechnic'),
                'delete' => auth()->user()->can('delete_polytechnic'),
                'view' => auth()->user()->can('view_polytechnic'),
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
    public function update(Request $request, $id)
    {
        $validate = $request->validate([
            'name' => 'required|max:250',
            'address' => 'required|max:250',
            'contact_number' => 'required',
            'institution_number' => 'required',
        ]);
        $updated = Polytechnic::findOrFail($id)->update($request->all());
        return Redirect::route('polytechnic.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Polytechnic::find($id)->delete();
        return Redirect::route('polytechnic.index');
    }
}
