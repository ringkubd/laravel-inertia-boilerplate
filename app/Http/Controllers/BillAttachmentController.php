<?php

namespace App\Http\Controllers;

use App\Models\AttachmentFolder;
use App\Models\BillAttachment;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class BillAttachmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('BillAttachment/Index');
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

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BillAttachment  $billAttachment
     * @return \Illuminate\Http\Response
     */
    public function show(BillAttachment $billAttachment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BillAttachment  $billAttachment
     * @return \Illuminate\Http\Response
     */
    public function edit(BillAttachment $billAttachment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\BillAttachment  $billAttachment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BillAttachment $billAttachment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BillAttachment  $billAttachment
     * @return \Illuminate\Http\Response
     */
    public function destroy(BillAttachment $billAttachment)
    {
        //
    }

    /**
     * @param Request $request
     * @return mixed
     */


    public function createFolder(Request $request){
        $request->validate([
            'name' => ['required',Rule::unique('attachment_folders')->where(function ($q) use($request){
                return $q->where('name', $request->name)->where('parent_id', $request->parent_id)->whereNull('deleted_at');
            }),
            ]
        ]);
        AttachmentFolder::create([
            'parent_id' => $request->parent_id,
            'name' => $request->name,
            'creator_id' => auth()->user()->id,
            'user_type' => auth()->user()->hasRole('student') ? 1 : 0
        ]);
        return redirect()->route('payment_prove.index')->withSuccess("Folder successfully created.");
    }

    /**
     * @param AttachmentFolder $folder
     * @return mixed
     */

    public function deleteFolder(AttachmentFolder $folder){
        AttachmentFolder::where('parent_id', $folder->id)->delete();
        $folder->delete();
        return redirect()->route('payment_prove.index')->withSuccess("Folder successfully deleted.");
    }

    public function uploadFile(Request $request){
        return dd($request->all());
    }

    /**
     * @param $base
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */

    public function folderList($base=false){
        return AttachmentFolder::query()
            ->when($base != false, function ($q) use($base){
                $q->where('parent_id', $base);
            }, function ($q) {
                $q->whereNull('parent_id');
            })->with('parent')->paginate();
    }
}
