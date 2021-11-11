<?php

namespace App\Models;

use App\Models\Traits\RecordsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use HasFactory, SoftDeletes, RecordsActivity;

    protected $guarded = ['id'];

    public function parentComment(){
        return $this->hasOne(Comment::class, 'parent_id');
    }

    public function childComment(){
        return $this->hasMany(Comment::class, 'id', 'parent_id');
    }
}
