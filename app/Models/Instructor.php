<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Instructor extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];


    public function basicInfo(){
        return $this->belongsTo(User::class, 'users_id', 'id');
    }

    public function madrasha(){
        return $this->belongsTo(Madrasha::class, 'madrasha_id');
    }

    public function trades(){
        return $this->belongsTo(Trade::class, 'trade_id');
    }
}
