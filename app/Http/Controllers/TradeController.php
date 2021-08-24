<?php

namespace App\Http\Controllers;

use App\Models\Trade;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TradeController extends Controller
{
    protected $component_route = "Trade/";
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view_trade');
        $trades = Trade::when($request->search, function ($q, $v) {
            $q->where('name', 'like', "%{$v}%");
        })->paginate();

        return Inertia::render($this->component_route."Index", [
            'trades' => $trades,
            'can' => [
                'create' => auth()->user()->can('create_trade'),
                'update' => auth()->user()->can('update_trade'),
                'delete' => auth()->user()->can('delete_trade'),
                'view' => auth()->user()->can('view_trade'),
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
        $this->authorize('create_trade');
        $trade = new Trade();
        return Inertia::render($this->component_route."Create", [
            'trade' => $trade,
            'can' => [
                'update' => auth()->user()->can('update_trade'),
                'delete' => auth()->user()->can('delete_trade'),
                'view' => auth()->user()->can('view_trade'),
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
        $this->authorize('create_trade');
        $trade = new Trade();
        $request->validate([
            'name' => 'required',
            'is_madrasa' => 'boolean'
        ]);
        $trade->create($request->all());
        return redirect()->route('trade.index')->withMessage("Trade successfully updated.");
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
        $this->authorize('update_trade');
        $trade = Trade::find($id);
        return Inertia::render($this->component_route."Edit", [
            'trade' => $trade,
            'can' => [
                'update' => auth()->user()->can('update_trade'),
                'delete' => auth()->user()->can('delete_trade'),
                'view' => auth()->user()->can('view_trade'),
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
        $this->authorize('update_trade');
        $trade = Trade::findOrFail($id);
        $request->validate([
            'name' => 'required',
            'is_madrasa' => 'boolean'
        ]);
        $trade->update($request->all());
        return redirect()->route('trade.index')->withMessage("Trade successfully updated.");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->authorize('delete_trade');
        Trade::findOrFail($id)->delete();
        return redirect()->route('trade.index')->withMessage("Trade successfully deleted.");
    }
}
