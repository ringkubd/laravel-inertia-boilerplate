<?php

namespace App\Models;

use App\Models\Traits\RecordsActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AcademicSession extends Model
{
    use HasFactory, SoftDeletes, RecordsActivity;

    protected $guarded = ['id'];

    protected static function booted()
    {
        static::addGlobalScope('relation', function (Builder $builder){
            $builder->orderByRaw("SUBSTRING_INDEX(session,',',1) desc");
        });
    }
}
