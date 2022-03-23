<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notice extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    public function classRoom(){
        return $this->belongsTo(ClassRoom::class, 'class_room_id');
    }
}
