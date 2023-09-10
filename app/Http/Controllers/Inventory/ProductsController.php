<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Inventory\Brand;
use App\Models\Inventory\Category;
use App\Models\Inventory\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        return Inertia::render('Inventory/Products/Index', [
            'data' => Product::query()
                ->with('meta','category', 'supplier', 'brand', 'class')
                ->latest()
                ->paginate(),
            'can' => []
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        return Inertia::render('Inventory/Products/Create', [
            'can' => [],
            'product' => new Product(),
            'units' => Product::selectRaw('unit as text')->groupBy('unit')->get(),
            'category' => Category::selectRaw('id, name as text')->get(),
            'brand' => Brand::selectRaw('id, IF(origin is not null, concat(name, " (", origin, ")"), name) as text')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'sl_no' => 'required|unique:products,sl_no',
            'unit' => 'required',
            'category_id' => 'required',
            'class_room_id' => 'required',
            'lesson' => 'required',
        ]);
        $product = Product::create($request->all());
        return redirect()->route('product.edit', $product->id);
    }

    /**
     * Display the specified resource.
     *
     * @param Product $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Product $product
     * @return \Inertia\Response
     */
    public function edit(Product $product)
    {
        return Inertia::render('Inventory/Products/Edit', [
            'can' => [],
            'product' => $product->load('meta'),
            'units' => Product::selectRaw('unit as text')->groupBy('unit')->get(),
            'category' => Category::selectRaw('id, name as text')->get(),
            'brand' => Brand::selectRaw('id, IF(origin is not null, concat(name, " (", origin, ")"), name) as text')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Product $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'title' => 'required',
            'sl_no' => 'required|unique:products,sl_no,'.$product->id,
            'unit' => 'required',
            'category_id' => 'required',
            'class_room_id' => 'required',
            'lesson' => 'required',
        ]);
        $product->update($request->all());
        return redirect()->route('product.edit', $product->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Product $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        if ($product){
            $product->meta()->delete();
            $product->delete();
        }
        return redirect()->back();
    }


    public function disable(Product $product){
        $product->update(['status' => !$product->status]);
        return redirect()->back();
    }

    /**
     * @param Product $product
     * @return void
     */
    public function addMeta(Product $product, Request $request){
        $request->validate([
            'key' => 'required',
            'content' => 'required',
        ]);
        $product->meta()->create($request->all());
        return redirect()->route('product.edit', $product->id);
    }
}
