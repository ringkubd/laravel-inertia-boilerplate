<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    public function parentCategory(){
        return $this->hasOne(Category::class, 'parent_id');
    }

    public function childCategory(){
        return $this->hasMany(Category::class, 'id', 'parent_id');
    }
}
