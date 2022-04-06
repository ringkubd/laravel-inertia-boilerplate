<?php

namespace App\Http\Controllers;

use App\Models\MobileApplication;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MobileApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $mobileApplication = MobileApplication::all();
        return Inertia::render('Mobile/Index', [
            'applications' => $mobileApplication,
            'can' => [
                'create' => auth()->user()->can('create_mobile'),
                'update' => auth()->user()->can('update_mobile'),
                'delete' => auth()->user()->can('delete_mobile'),
                'view' => auth()->user()->can('view_mobile'),
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
        return Inertia::render('Mobile/Create');
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
            'name' => 'required',
            'platform' => 'required',
            'file_path' => 'required',
            'version_code' => 'required',
        ]);
        $file = $request->file('file_path');
        $name = $request->version_code.'_'.$request->platform.'_'.$file->getClientOriginalName().'.'.$file->getClientOriginalExtension();
        $file->move(public_path('mobile_app'), $name);

        $mobile = MobileApplication::create([
            'file_path' => '/mobile_app/'.$name,
            'name' => $request->name,
            'platform' => $request->platform,
            'version_code' => $request->version_code,
            'status' => $request->status ?? 1
        ]);
        return redirect()->route('mobile.index')->withSuccess("Application successfully uploaded.");

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\MobileApplication  $mobileApplication
     * @return \Illuminate\Http\Response
     */
    public function show(MobileApplication $mobile)
    {
        $file= public_path($mobile->file_path);
        $mobile->update(['number_of_download' => $mobile->number_of_download + 1]);
        return response()->download($file, '', ['location' => url('/')]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\MobileApplication  $mobileApplication
     * @return \Illuminate\Http\Response
     */
    public function edit(MobileApplication $mobileApplication)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\MobileApplication  $mobileApplication
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MobileApplication $mobileApplication)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\MobileApplication  $mobileApplication
     * @return \Illuminate\Http\Response
     */
    public function destroy(MobileApplication $mobile)
    {
        unlink(public_path().$mobile->file_path);
        $mobile->delete();
        return redirect()->route('mobile.index')->withSuccess("Application successfully uploaded.");
    }
}
