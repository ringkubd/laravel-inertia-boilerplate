<?php

namespace App\Http\Controllers;

use App\Models\Madrasha;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class MadrasaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view_madrasa');
        $data = Madrasha::when($request->search, function ($q, $v) {
            $q->where('name', 'like',"%".$v."%");
        })->get();
        return Inertia::render('Madrasa/Index', [
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
        $this->authorize('create_madrasa');
        $data = new Madrasha();
        return Inertia::render('Madrasa/Create', [
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
        $this->authorize('create_madrasa');
        $validate = $request->validate([
            'name' => 'required|max:250',
            'address' => 'required|max:250',
            'email' => 'required|email',
            'district' => 'required',
            'mobile' => 'required',
        ]);
        Madrasha::create($request->all());
        return Redirect::route('madrasa.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->authorize('view_madrasa');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->authorize('update_madrasa');
        $data = Madrasha::find($id);
        return Inertia::render('Madrasa/Edit', [
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->authorize('update_madrasa');
        $validate = $request->validate([
            'name' => 'required|max:250',
            'address' => 'required|max:250',
            'email' => 'required|email',
            'district' => 'required',
            'mobile' => 'required',
        ]);
        Madrasha::find($id)->update($request->all());
        return Redirect::route('madrasa.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->authorize('delete_madrasa');
        Madrasha::find($id)->delete();
        return Redirect::route('madrasa.index');
    }
}
