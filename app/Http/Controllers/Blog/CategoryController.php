<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoryController extends Controller
{
    private $component_base = "Blog/Admin/Category/";
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return Inertia::render($this->component_base.'Index', [
            "categories" => Category::query()
                ->when($request->search, function ($query, $va){
                    $query->where('title','like', "%$va%" );
                })
                ->with('childCategory')
                ->whereNull('parent_id')
                ->paginate()
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Category::where('parent_id', $id)->update(['parent_id', null]);
        Category::find($id)->delete();
        return redirect()->route('category.index')->withFlash('success', 'Category deleted successfully');
    }

    /**
     * @param Request $request
     * @return string
     */
    public function getCategory(Request $request)
    {
        return Category::query()
            ->when($request->title, function ($q, $v){
                $q->where('title', 'like', "%$v%");
            })
            ->paginate(50)
            ->toJson();
    }

    /**
     * @param Request $request
     * @return string
     */
    public function getTag(Request $request)
    {
        return Tag::query()
            ->when($request->title, function ($q, $v){
                $q->where('title', 'like', "%$v%");
            })
            ->paginate(50)
            ->toJson();
    }

    /**
     * @param Request $request
     * @return string
     */
    public function createTag(Request $request)
    {
        $slug = str_replace(' ', '_',$request->title);
        return Tag::query()
            ->create(['title' => $request->title, 'meta_title' => $request->title, 'slug' => $slug]);
    }
}
