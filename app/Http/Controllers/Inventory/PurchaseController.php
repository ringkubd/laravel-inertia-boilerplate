<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Inventory\IdbStock;
use App\Models\Inventory\Product;
use App\Models\Inventory\Purchase;
use App\Models\Inventory\Supplier;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('Inventory/Purchase/Index', [
            'can' => [],
            'data' => Purchase::query()
                ->with('product')
                ->with('supplier')
                ->with('idbStock')
                ->with('madrasahStock')
                ->latest()
                ->paginate(20),
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
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'quantity' => 'required',
            'unit_price' => 'required',
            'total_price' => 'required',
            'purchase_date' => 'required',
        ]);
        $idbStocks = IdbStock::where('product_id',$request->product_id)->first();
        $purchase = Purchase::create($request->all());
        IdbStock::updateOrCreate([
            'product_id' => $request->product_id
        ], [
            'stock' => ($idbStocks->stock ?? 0) + $request->quantity
        ]);
        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Inventory\Purchase  $purchase
     * @return \Illuminate\Http\Response
     */
    public function show(Purchase $purchase)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Inventory\Purchase  $purchase
     * @return \Illuminate\Http\Response
     */
    public function edit(Purchase $purchase)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Inventory\Purchase  $purchase
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Purchase $purchase)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Inventory\Purchase  $purchase
     * @return \Illuminate\Http\Response
     */
    public function destroy(Purchase $purchase)
    {
        //
    }


    public function products(Request $request){
        return Product::query()
            ->selectRaw("products.id, concat(title, '_', c.name, '_', cr.name) as text")
            ->when($request->search, function ($q, $s){
                $q->where('title', 'like', "%$s%")
                    ->orWhere('cr.name', 'like', "%$s%")
                    ->orWhere('c.name', 'like', "%$s%");
            })
            ->join('categories as c', 'c.id', 'products.category_id')
            ->join('class_rooms as cr', 'cr.class_name_number', 'products.class_room_id')
            ->latest('products.created_at')
            ->limit(30)
            ->get();
    }

    public function suppliers(Request $request){
        return Supplier::query()
            ->selectRaw("suppliers.id, concat(suppliers.name, '_', c.name) as text")
            ->when($request->search, function ($q, $s){
                $q->where('suppliers.name', 'like', "%$s%")
                    ->orWhere('contact_number', 'like', "%$s%")
                    ->orWhere('c.name', 'like', "%$s%")
                    ->orWhere('contact_person', 'like', "%$s%");
            })
            ->join('categories as c', 'c.id', 'suppliers.category_id')
            ->latest('suppliers.created_at')
            ->limit(30)
            ->get();
    }
    public function productStocks(Product $product){
        return $product->load(['idbStocks' => function($q){
            $q->join('madrashas as m', 'm.id', 'idb_stocks.product_id')
                ->selectRaw('m.name, idb_stocks.product_id, idb_stocks.stock');
        }])->load(['madrasahStores' => function($q){
            $q->join('madrashas as m', 'm.id', 'madrasah_stores.product_id')
                ->selectRaw('m.name, madrasah_stores.product_id, madrasah_stores.stock');
        }]);
    }
}
