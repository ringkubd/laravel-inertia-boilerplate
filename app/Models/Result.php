<?php

namespace App\Models;

use App\Models\Traits\RecordsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Result extends Model
{
    use HasFactory, SoftDeletes, RecordsActivity;

    protected $guarded = ['id'];

    public function details(){
        return $this->hasOne(ResultDetails::class);
    }

    public function attachments(){
        return $this->hasMany(ResultDocument::class);
    }
}
