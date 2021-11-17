<?php

namespace App\Models;

use App\Models\Traits\RecordsActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Result extends Model
{
    use HasFactory, SoftDeletes, RecordsActivity;

    protected $guarded = ['id'];

    protected static function booted()
    {
        static::addGlobalScope('student', function (Builder $builder){
            $builder->with('addedBy', 'approvedBy', 'attachments');
        });
    }

    public function student(){
        return $this->belongsTo(Student::class);
    }

    public function addedBy(){
        return $this->belongsTo(User::class, 'added_by');
    }

    public function approvedBy(){
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function details(){
        return $this->hasOne(ResultDetails::class);
    }

    public function attachments(){
        return $this->hasMany(ResultDocument::class);
    }
}
