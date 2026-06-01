<?php

namespace App\Models;

use App\Models\Traits\RecordsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClassRoom extends Model
{
    use HasFactory, SoftDeletes, RecordsActivity;

    protected $guarded = ['id'];

    protected $casts = [
        'schedule' => 'array',
    ];

    public function students(){
        return $this->hasManyThrough(Student::class, 'class_room_students');
    }

    public function studentEnrollments(){
        return $this->belongsToMany(Student::class, 'class_room_students');
    }

    public function teacher(){
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function attendances(){
        return $this->hasMany(StudentAttendance::class, 'class_room_id');
    }
}
