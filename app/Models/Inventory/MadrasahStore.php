<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MadrasahStore extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];
}
