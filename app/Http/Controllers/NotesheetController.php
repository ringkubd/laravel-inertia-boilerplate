<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Notesheet;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NotesheetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $invoice = Invoice::query()
            ->whereNull('notesheet_id')->get();
        return Inertia::render('NoteSheet/Index', [
            'invoices' => $invoice
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Notesheet  $notesheet
     * @return \Illuminate\Http\Response
     */
    public function show(Notesheet $notesheet)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Notesheet  $notesheet
     * @return \Illuminate\Http\Response
     */
    public function edit(Notesheet $notesheet)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Notesheet  $notesheet
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Notesheet $notesheet)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Notesheet  $notesheet
     * @return \Illuminate\Http\Response
     */
    public function destroy(Notesheet $notesheet)
    {
        //
    }
}
