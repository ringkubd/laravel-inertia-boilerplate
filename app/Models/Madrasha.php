<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Madrasha extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function students(){
        return $this->hasMany(Student::class);
    }
}
