<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TeacherAttendance extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = ['id'];

    public function madrasah(){
        return $this->belongsTo(Madrasha::class, 'madrasha_id', 'id');
    }

    public function creator(){
        return $this->hasOne(User::class, 'id','creator_id');
    }
}
