<?php

namespace App\Http\Controllers;

use App\Models\FeeType;
use App\Models\NotesheetTemplate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class NoteSheetTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(): Response
    {
        $note_sheet_templates = NotesheetTemplate::with('fee_types')->latest()->get();
        return Inertia::render('NoteSheetTemplate/Index', [
            'note_sheet_templates' => $note_sheet_templates,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(): Response
    {
        $feeTypes = FeeType::selectRaw('id as value, name as label')->get();
        $noteTemplate = new NotesheetTemplate();
        return Inertia::render('NoteSheetTemplate/Create', [
            'fee_types' => $feeTypes,
            'note_template' => $noteTemplate,
            'selected_fee_types' => [],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => 'required',
            'fee_type' => 'nullable|array',
            'content' => 'required'
        ]);

        $invoiceTemplate = NotesheetTemplate::create([
            'title' => $request->title,
            'content' => $request->input('content'),
            'user_id' => auth()->user()->id
        ]);
        if (!empty($request->fee_type)) {
            $invoiceTemplate->fee_types()->attach($request->fee_type);
        }
        return redirect()->route('note_sheet_template.index')->withSuccess("Notesheet Template created.");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\NotesheetTemplate  $note_sheet_template
     * @return RedirectResponse
     */
    public function show(NotesheetTemplate $note_sheet_template): RedirectResponse
    {
        return redirect()->route('note_sheet_template.edit', $note_sheet_template->id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\NotesheetTemplate  $note_sheet_template
     * @return Response
     */
    public function edit($note_sheet_template): Response
    {
        $feeTypes = FeeType::selectRaw('id as value, name as label')->get();
        $noteTemplate = NotesheetTemplate::with(['fee_types' => function ($q) {
            $q->selectRaw('id as value, name as label');
        }])->find($note_sheet_template);

        return Inertia::render('NoteSheetTemplate/Edit', [
            'fee_types' => $feeTypes,
            'note_template' => $noteTemplate,
            'selected_fee_types' => $noteTemplate->fee_types->pluck('value')->toArray(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\NotesheetTemplate  $note_sheet_template
     * @return RedirectResponse
     */
    public function update(Request $request, NotesheetTemplate $note_sheet_template): RedirectResponse
    {
        $request->validate([
            'title' => 'required',
            'fee_type' => 'nullable|array',
            'content' => 'required'
        ]);
        $note_sheet_template->update($request->only('title', 'content'));
        $note_sheet_template->fee_types()->sync($request->fee_type ?? []);
        return redirect()->route('note_sheet_template.index')->withSuccess("Notesheet Template updated.");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\NotesheetTemplate  $note_sheet_template
     * @return RedirectResponse
     */
    public function destroy(NotesheetTemplate $note_sheet_template): RedirectResponse
    {
        $note_sheet_template->fee_types()->detach();
        $note_sheet_template->delete();
        return redirect()->route('note_sheet_template.index')->withSuccess("Notesheet Template successfully deleted.");
    }
}
