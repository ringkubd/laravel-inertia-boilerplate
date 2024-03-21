<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Placement extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function status()
    {
        return $this->morphTo('present_status');
    }

    public function student(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function getCurrentStatusAttribute(): string
    {
        $status = "";
        switch ($this->present_status_type){
            case 'App\Models\FurtherEducation':
                $status = 'Higher Study';
                break;
            case 'App\Models\Employment':
                $status = 'Job';
                break;
            case 'App\Models\OtherPlacementStatus':
                $status = 'Other';
                break;
        }
        return $status;
    }
}
