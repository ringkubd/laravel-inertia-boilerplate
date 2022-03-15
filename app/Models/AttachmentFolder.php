<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttachmentFolder extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    public function parent(){
        return $this->belongsTo(AttachmentFolder::class, 'parent_id');
    }
}
