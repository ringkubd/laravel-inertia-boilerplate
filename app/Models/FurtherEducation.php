<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FurtherEducation extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = "further_educations";
    protected $guarded = [];

    public function placement(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Placement::class, 'present_status');
    }
}
