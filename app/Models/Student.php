<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function users(){
        return $this->belongsTo(User::class);
    }

    public function madrasha(){
        return $this->belongsTo(Madrasha::class, 'madrasha_id', 'id');
    }

    public function polytechnic(){
        return $this->belongsTo(Polytechnic::class, 'polytechnic', 'id');
    }

    public function studentConfiguration(){
        return $this->hasOne(StudentConfiguration::class, 'users_id', 'users_id');
    }
}
