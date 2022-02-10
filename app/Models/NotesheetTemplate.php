<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NotesheetTemplate extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = ['id'];

    public function fee_types(){
        return $this->belongsToMany(FeeType::class, 'notesheet_template_fee_type');
    }
}
