<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;

class CategoryController extends Controller
{
    private $component_base = "Blog/Admin/Category/";

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Response
     * @throws AuthorizationException
     */
    public function index(Request $request)
    {
        $this->authorize('view_category');
        return Inertia::render($this->component_base.'Index', [
            "categories" => BlogCategory::query()
                ->when($request->search, function ($query, $va){
                    $query->where('title','like', "%$va%" );
                })
                ->with('childCategory')
                ->with('parentCategory')
                ->paginate(),
            "can" => [
                'create' => auth()->user()->can('create_category'),
                'update' => auth()->user()->can('update_category'),
                'delete' => auth()->user()->can('delete_category'),
                'view' => auth()->user()->can('view_category'),
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $this->authorize('create_category');
        $category = new BlogCategory();
        return Inertia::render($this->component_base.'Create', [
            'category' => $category,
            'parent' => ""
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     * @throws AuthorizationException
     */
    public function store(Request $request)
    {
        $this->authorize('create_category');
        $category =  new BlogCategory();
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
     * @return Response
     */
    public function show($id)
    {
        $category = BlogCategory::find($id);
        $category_post = Post::whereHas('categories', function ($q) use ($id) {
            $q->where('blog_categories.id', $id);
        })->with('author')->with('tags')->get();
        return Inertia::render($this->component_base.'Preview', [
            'category' => $category,
            'category_post' => $category_post
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Response
     * @throws AuthorizationException
     */
    public function edit($id)
    {
        $this->authorize('update_category');
        $category = BlogCategory::with('parentCategory')->find($id);
        return Inertia::render($this->component_base.'Edit', [
            'category' => $category,
            'parent' => $category->parentCategory->id ?? ""
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     * @throws AuthorizationException
     */
    public function update(Request $request, $id)
    {
        $this->authorize('update_category');
        $category =  BlogCategory::find($id);
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
     * @param int $id
     * @return Response
     * @throws AuthorizationException
     */
    public function destroy($id)
    {
        $this->authorize('delete_category');
        BlogCategory::where('parent_id', $id)->update(['parent_id' =>  null]);
        BlogCategory::find($id)->delete();
        return redirect()->route('category.index')->withFlash('success', 'Category deleted successfully');
    }

    /**
     * @param Request $request
     * @return string
     */
    public function getCategory(Request $request)
    {
        return BlogCategory::query()
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
