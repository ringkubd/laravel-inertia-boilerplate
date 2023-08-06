<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function getCategoryNameAttribute(){
        return $this->category?->name;
    }

    public function category(){
        return $this->belongsTo(Category::class);
    }
}
