<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RequisitionProduct extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $table = 'requisition_product';
}
