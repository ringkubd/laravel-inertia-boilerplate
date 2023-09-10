<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Purchase extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function product(){
        return $this->belongsTo(Product::class);
    }
    public function supplier(){
        return $this->belongsTo(Supplier::class);
    }

    public function idbStock(){
        return $this->hasOne(IdbStock::class, 'product_id', 'product_id');
    }

    public function madrasahStock(){
        return $this->hasMany(MadrasahStore::class, 'product_id', 'product_id');
    }
}
