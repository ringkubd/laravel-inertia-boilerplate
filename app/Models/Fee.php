<?php

namespace App\Models;

use App\Models\Traits\RecordsActivity;
use Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fee extends Model
{
    use HasFactory, RecordsActivity, Compoships;

    protected $guarded = ['id'];
}
