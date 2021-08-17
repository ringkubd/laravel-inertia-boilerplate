<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class PageController extends Controller
{
    private $component = "Blog/Admin/Page/";
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view_page');
        $posts = Post::query()
            ->with('categories', 'comments', 'tags', 'author', 'metas')
            ->when($request->search, function($q, $v){
                $q->where('title', 'like', "%{$v}%")
                    ->orWhere('slug', 'like', "%{$v}%")
                    ->orWhere('content', 'like', "%{$v}%");
            })
            ->where('post_type', 'page')
            ->paginate();

        return Inertia::render($this->component . 'Index', [
            'posts' => $posts,
            'can' => [
                'create' => auth()->user()->can('create_page'),
                'update' => auth()->user()->can('update_page'),
                'delete' => auth()->user()->can('delete_page'),
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create_page');
        return Inertia::render($this->component . 'Create',  [
            'post' => new Post(),
            'categories' => [],
            'tags' => []
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
        $this->authorize('create_page');
        $request->validate([
            'title' => 'required|unique:posts',
            'meta_title' => 'required|unique:posts',
            'slug' => 'required|unique:posts',
            'content' => 'required',
            'post_status' => 'required',
            'categories' => 'required',
            'author' => 'required'
        ]);
        $data = $request->all();
        $tags = $data['tags'];
        unset($data['tags']);
        $categories = $data['categories'];
        unset($data['categories']);
        unset($data['summary']);
        $data['published_at'] = now();
        $post = Post::create($data);
        $post->categories()->sync($categories);
        $tags = array_map(function($tag){
            if ((int)$tag == 0) {
                $tag = Tag::where('title', $tag)->first();
                $tag = $tag->id;
            }
            return $tag;
        }, $tags);
        $post->tags()->sync($tags);
        return redirect()->route('page.index')->withFlash('success', 'Post stored successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if ((int) $id == 0 ) {
            $post = Post::query()
                ->where('slug', $id)
                ->with('categories')
                ->with('tags')
                ->with('author')
                ->where('post_type', 'page')
                ->firstOrFail();
        }else{
            $post = Post::query()
                ->where('id', $id)
                ->with('categories')
                ->with('tags')
                ->with('author')
                ->where('post_type', 'page')
                ->firstOrFail();
        }


        $related_post = $this->postByCategories($post->categories->pluck('title')->toArray(), $id);

        return Inertia::render($this->component . 'Preview', [
            'post' => $post,
            'related_post' => $related_post
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
        $this->authorize('update_page');
        $post = Post::query()
            ->where('id', $id)
            ->with('categories')
            ->with('tags')
            ->firstOrFail();
        $categories = $post->categories->pluck('id')->toArray();
        $tags = $post->tags->pluck('id')->toArray();

        return Inertia::render($this->component . 'Edit', [
            'post' => $post,
            'categories' => $categories,
            'tags' => $tags
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(Request $request, $id)
    {
        $this->authorize('update_page');
        $request->validate([
            'title' => 'required',
            'meta_title' => 'required',
            'slug' => 'required',
            'content' => 'required',
            'post_status' => 'required',
            'categories' => 'required',
            'author' => 'required'
        ]);
        $data = $request->all();
        $tags = $data['tags'];
        unset($data['tags']);
        $categories = $data['categories'];
        unset($data['categories']);
        $data['summary'] = Str::words($data['summary'], 100);
        $post = Post::find($id);
        $post->update($data);
        $post->categories()->sync($categories);
        $tags = array_map(function($tag){
            if ((int)$tag == 0) {
                $tag = Tag::where('title', $tag)->first();
                $tag = $tag->id;
            }
            return $tag;
        }, $tags);
        $post->tags()->sync($tags);
        return redirect()->route('page.index')->withFlash('success', 'Page updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->authorize('delete_page');
        if (auth()->user()->can('delete_page')) {
            $post = Post::find($id)->delete();
            return redirect()->route('page.index')->withFlash('success', 'Post deleted successfully');
        }
        abort(403);
    }

    /**
     * @param array $categories
     * @return mixed
     */

    private function postByCategories(Array $categories, $slug){
        return Post::query()
            ->whereHas('categories', function ($q) use($categories) {
                $q->whereIn('title', $categories);
            })
            ->where('slug', '!=', $slug)
            ->wherePostStatus('active')
            ->with('categories')
            ->with('tags')
            ->with('author')
            ->limit(5)
            ->get();
    }
}
