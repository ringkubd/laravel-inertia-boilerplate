<?php

namespace App\Models\Inventory;

use App\Models\ClassRoom;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function meta(){
        return $this->hasMany(ProductMeta::class);
    }

    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function supplier(){
        return $this->belongsTo(Supplier::class);
    }
    public function brand(){
        return $this->belongsTo(Brand::class);
    }
    public function class(){
        return $this->belongsTo(ClassRoom::class, 'class_room_id','class_name_number');
    }
    public function idbStocks(){
        return $this->hasOne(IdbStock::class);
    }
    public function madrasahStores(){
        return $this->hasMany(MadrasahStore::class);
    }
}
