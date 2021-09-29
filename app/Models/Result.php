<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function details(){
        return $this->hasOne(ResultDetails::class);
    }

    public function attachments(){
        return $this->hasMany(ResultDocument::class);
    }
}
