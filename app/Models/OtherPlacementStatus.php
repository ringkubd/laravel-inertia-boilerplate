<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OtherPlacementStatus extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function placement(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Placement::class, 'present_status');
    }
}
