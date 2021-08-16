<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Intervention\Image\Facades\Image;

class PostController extends Controller
{
    private $component = "Blog/Admin/Post/";

    public function __construct(){
        $this->authorizeResource(Post::class);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        $posts = Post::query()
            ->with('categories', 'comments', 'tags', 'author', 'metas')
            ->when($request->search, function($q, $v){
                $q->where('title', 'like', "%{$v}%")
                    ->orWhere('slug', 'like', "%{$v}%")
                    ->orWhere('content', 'like', "%{$v}%");
            })
            ->where('post_type', 'post')
            ->paginate();

        return Inertia::render($this->component . 'Index', [
            'posts' => $posts,
            'can_edit' => auth()->user()->hasPermissionTo('update_post'),
            'can_delete' => auth()->user()->hasPermissionTo('delete_post'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
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
        return redirect()->route('post.index')->withFlash('success', 'Post stored successfully');
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
                ->first();
        }else{
            $post = Post::query()
                ->where('id', $id)
                ->with('categories')
                ->with('tags')
                ->with('author')
                ->first();
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
        $post = Post::query()
            ->where('id', $id)
            ->with('categories')
            ->with('tags')
            ->first();
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
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
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
        return redirect()->route('post.index')->withFlash('success', 'Post updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (auth()->user()->can('delete_post')) {
            $post = Post::find($id)->delete();
            return redirect()->route('post.index')->withFlash('success', 'Post deleted successfully');
        }
        abort(403);
    }

    /**
     * @param Request $request
     * @return string|void
     */

    public function fileUpload(Request $request){
        if($request->hasFile('thumbnail')) {
            $origin_Name = $request->file('thumbnail')->getClientOriginalName();
            $File_Name = pathinfo($origin_Name, PATHINFO_FILENAME);
            $extension_Name = $request->file('thumbnail')->getClientOriginalExtension();
            $File_Name = $File_Name.'_'.time().'.'.$extension_Name;

            $image = $request->file('thumbnail');
            $url = 'images/'.$File_Name;

            $img = Image::make($image->getRealPath());
            $img->resize(config('setup.thumbnail.width'), config('setup.thumbnail.height'), function($constraint){
                $constraint->aspectRatio();
            })->save(public_path('/images/thumbnail') .'/'. $File_Name);

            $request->file('thumbnail')->move(public_path('images'), $File_Name);
            return asset('/images/thumbnail/'. $File_Name);
        }
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
