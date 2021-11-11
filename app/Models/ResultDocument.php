<?php

namespace App\Models;

use App\Models\Traits\RecordsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ResultDocument extends Model
{
    use HasFactory, SoftDeletes, RecordsActivity;

    protected $guarded = ['id'];

    public function result(){
        return $this->belongsTo(Result::class, 'result_id', 'id');
    }
}
