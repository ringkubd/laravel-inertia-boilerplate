<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        //
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
