<?php

namespace App\Models;

use App\Models\Traits\RecordsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlogCategory extends Model
{
    use HasFactory, SoftDeletes, RecordsActivity;

    protected $guarded = ['id'];

    public function parentCategory(){
        return $this->hasOne(BlogCategory::class, 'id', 'parent_id');
    }

    public function childCategory(){
        return $this->hasMany(BlogCategory::class, 'id', 'parent_id');
    }
}
