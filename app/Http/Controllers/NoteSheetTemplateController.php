<?php

namespace App\Http\Controllers;

use App\Models\FeeType;
use App\Models\NotesheetTemplate;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NoteSheetTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $note_sheet_templates = NotesheetTemplate::with('fee_types')->get();
        return Inertia::render('NoteSheetTemplate/Index', [
            'note_sheet_templates' => $note_sheet_templates,
            'can' => [
                'create' => auth()->user()->can('create_note_sheet_template'),
                'update' => auth()->user()->can('update_note_sheet_template'),
                'delete' => auth()->user()->can('delete_note_sheet_template'),
                'view' => auth()->user()->can('view_note_sheet_template'),
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
        $feeTypes = FeeType::selectRaw('id as value, name as label')->where('is_madrasa', 0)->get();
        $noteTemplate = new NotesheetTemplate();
        return Inertia::render('NoteSheetTemplate/Create', [
            'fee_types' => $feeTypes,
            'note_template' => $noteTemplate,
            'selected_fee_types' => [],
            'can' => [
                'create' => auth()->user()->can('create_note_sheet_template'),
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
            'title' => 'required',
            'fee_type' => 'required',
            'content' => 'required'
        ]);

        $invoiceTemplate = NotesheetTemplate::create([
            'title' => $request->title,
            'content' => $request->input('content'),
            'user_id' => auth()->user()->id
        ]);
        $invoiceTemplate->fee_types()->attach($request->fee_type);
        return redirect()->route('note_sheet_template.index')->withSuccess("Notesheet Template created.");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\NotesheetTemplate  $notesheetTemplate
     * @return \Illuminate\Http\Response
     */
    public function show(NotesheetTemplate $note_sheet_template)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\NotesheetTemplate  $notesheetTemplate
     * @return \Illuminate\Http\Response
     */
    public function edit($note_sheet_template)
    {
        $feeTypes = FeeType::selectRaw('id as value, name as label')->where('is_madrasa', 0)->get();
        $noteTemplate = NotesheetTemplate::with(['fee_types' => function($q){
            $q->selectRaw('id as value, name as label');
        }])->find($note_sheet_template);

        return Inertia::render('NoteSheetTemplate/Edit', [
            'fee_types' => $feeTypes,
            'note_template' => $noteTemplate,
            'selected_fee_types' => $noteTemplate->fee_types->pluck('value')->toArray(),
            'can' => [
                'create' => auth()->user()->can('create_note_sheet_template'),
            ]
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\NotesheetTemplate  $notesheetTemplate
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, NotesheetTemplate $note_sheet_template)
    {
        $request->validate([
            'title' => 'required',
            'fee_type' => 'required',
            'content' => 'required'
        ]);
        $note_sheet_template->update($request->only('title', 'content'));
        $note_sheet_template->fee_types()->sync($request->fee_type);
        return redirect()->route('note_sheet_template.index')->withSuccess("Notesheet Template updated.");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\NotesheetTemplate  $notesheetTemplate
     * @return \Illuminate\Http\Response
     */
    public function destroy(NotesheetTemplate $note_sheet_template)
    {
        $note_sheet_template->fee_types()->detach();
        $note_sheet_template->delete();
        return redirect()->route('note_sheet_template.index')->withSuccess("Notesheet Template successfully deleted.");
    }
}
