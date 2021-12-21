<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherAttendance extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function madrasah(){
        return $this->hasOne(Madrasha::class);
    }

    public function creator(){
        return $this->hasOne(User::class, 'creator_id');
    }
}
