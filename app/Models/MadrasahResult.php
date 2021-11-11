<?php

namespace App\Models;

use App\Models\Traits\RecordsActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MadrasahResult extends Model
{
    use HasFactory, RecordsActivity;

    protected $guarded = ['id'];

    protected static function booted()
    {
        static::addGlobalScope('student', function (Builder $builder){
           $builder->with('student');
        });
    }

    public function student(){
        return $this->belongsTo(Student::class);
    }

}
