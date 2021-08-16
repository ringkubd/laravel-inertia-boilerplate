<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoryController extends Controller
{
    private $component_base = "Blog/Admin/Category/";

    public function __construct(){
        $this->authorizeResource(Category::class);
    }
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
                ->with('parentCategory')
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
        $category = new Category();
        return Inertia::render($this->component_base.'Create', [
            'category' => $category,
            'parent' => ""
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
        $category =  new Category();
        $request->validate([
            'title' => 'required',
            'meta_title' => 'required'
        ]);
        $category->create($request->all());
        return redirect()->route('category.index')->withFlash('success', 'Category store successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $category = Category::find($id);
        $category_post = Post::whereHas('categories', function ($q) use ($id) {
            $q->where('categories.id', $id);
        })->with('author')->with('tags')->get();
        return Inertia::render($this->component_base.'Preview', [
            'category' => $category,
            'category_post' => $category_post
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $category = Category::with('parentCategory')->find($id);
        return Inertia::render($this->component_base.'Edit', [
            'category' => $category,
            'parent' => $category->parentCategory->id ?? ""
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
        $category =  Category::find($id);
        $request->validate([
            'title' => 'required',
            'meta_title' => 'required'
        ]);
        $category->update($request->all());
        return redirect()->route('category.index')->withFlash('success', 'Category updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Category::where('parent_id', $id)->update(['parent_id' =>  null]);
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
