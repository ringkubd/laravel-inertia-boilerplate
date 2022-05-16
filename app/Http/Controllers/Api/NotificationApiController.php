<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notice;
use Illuminate\Http\Request;

class NotificationApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $student = auth()->user()->student;
        try {
            $notice = Notice::query()
                ->select('notices.*', 'notice_seen.seen_at')
                ->where('academic_session', $student?->polytechnic_session)
                ->orWhereNull('academic_session')
                ->leftJoin('notice_seen', function ($join){
                    $join->on('notice_seen.notice_id', '=', 'notices.id')->where('user_id', auth()->user()->id);
                })
                ->latest()->get();
            return sendResponse($notice, 'Notice successfully retrived.');
        }catch (\Exception $exception){
            return sendError($exception->getMessage());
        }
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $notice = Notice::find($id);
        return $notice->seen()->sync(auth()->user()->id);
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
