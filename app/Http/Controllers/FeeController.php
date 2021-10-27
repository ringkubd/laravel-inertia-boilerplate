<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Models\Fee;
use App\Models\FeeType;
use App\Models\Trade;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class FeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view_fee');
        $fees =  Fee::query()
            ->when($request->search, function($q, $v){
                $q->where('session', 'like', "%$v%")
                    ->orWhere('trade', 'like', "%$v%")
                    ->orWhere('fee_type', 'like', "%$v%");
            })
            ->paginate();
        return Inertia::render('Fee/Index', [
            'data' => $fees,
            'can' => [
                'create' => auth()->user()->can('create_fee'),
                'update' => auth()->user()->can('update_fee'),
                'delete' => auth()->user()->can('delete_fee'),
                'view' => auth()->user()->can('view_fee'),
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
        $this->authorize('create_fee');
        return Inertia::render('Fee/Create',[
            'sessions' => AcademicSession::all(),
            'trades' => Trade::where('is_madrasa', 0)->get(),
            'fee_types' => FeeType::all(),
            'fee' => new Fee(),
            'can' => [
                'create' => auth()->user()->can('create_fee'),
                'update' => auth()->user()->can('update_fee'),
                'delete' => auth()->user()->can('delete_fee'),
                'view' => auth()->user()->can('view_fee'),
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
        $this->authorize('create_fee');
        $request->validate([
            'academic_session' => ['required'],
            'trade' => ['required', Rule::unique('fees')->where(function ($query) use($request) {
                return $query->where('session', $request->academic_session)
                    ->where('semester', $request->semester)
                    ->where('trade', $request->trade)
                    ->where('fee_type', $request->fee_type);
            })],
            'semester' => ['required'],
            'fee_type' => ['required'],
            'amount' => ['required'],
        ], [
            'trade.unique' => "This semester {$request->fee_type} bill already submitted."
        ]);

        Fee::create([
            'session' => $request->academic_session,
            'trade' =>  $request->trade,
            'semester' =>  $request->semester,
            'fee_type' =>  $request->fee_type,
            'amount' =>  $request->amount,
        ]);
        return redirect()->route('fee.index')->withSuccess("Fee added successfully.");
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
    public function edit(Fee $fee)
    {
        $this->authorize('update_fee');
        return Inertia::render('Fee/Edit',[
            'sessions' => AcademicSession::all(),
            'trades' => Trade::where('is_madrasa', 0)->get(),
            'fee_types' => FeeType::all(),
            'fee' => $fee,
            'can' => [
                'create' => auth()->user()->can('create_fee'),
                'update' => auth()->user()->can('update_fee'),
                'delete' => auth()->user()->can('delete_fee'),
                'view' => auth()->user()->can('view_fee'),
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
    public function update(Request $request, Fee $fee)
    {
        $this->authorize('update_fee');
        $request->validate([
            'academic_session' => ['required'],
            'trade' => ['required', Rule::unique('fees')->where(function ($query) use($request, $fee) {
                return $query->where('session', $request->academic_session)
                    ->where('semester', $request->semester)
                    ->where('trade', $request->trade)
                    ->whereNotIn('id', [$fee->id])
                    ->where('fee_type', $request->fee_type);
            })],
            'semester' => ['required'],
            'fee_type' => ['required'],
            'amount' => ['required'],
        ], [
          'trade.unique' => "This semester {$request->fee_type} already submitted."
        ]);

        $fee->update([
            'session' => $request->academic_session,
            'trade' =>  $request->trade,
            'semester' =>  $request->semester,
            'fee_type' =>  $request->fee_type,
            'amount' =>  $request->amount,
        ]);
        return redirect()->route('fee.index')->withSuccess("Fee Updated successfully.");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Fee $fee)
    {
        $this->authorize('delete_fee');
        $fee->delete();
        return redirect()->route('fee.index')->withSuccess("Fee Deleted successfully.");
    }
}
