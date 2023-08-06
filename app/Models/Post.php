<?php

namespace App\Models;

use App\Models\Traits\RecordsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory, SoftDeletes, RecordsActivity;

    protected $guarded = ['id'];

    public function author(){
        return $this->hasOne(User::class, 'id', 'author');
    }

    public function categories(){
        return $this->belongsToMany(BlogCategory::class, 'post_category')->whereNull('parent_id')->with('childCategory');
    }

    public function comments(){
        return $this->hasMany(Comment::class)->whereNull('parent_id')->with('childComment');
    }

    public function tags(){
        return $this->belongsToMany(Tag::class);
    }

    public function metas(){
        return $this->hasMany(PostMeta::class);
    }
}
