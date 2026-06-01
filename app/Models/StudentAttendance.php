<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentAttendance extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'date' => 'date:Y-m-d',
        'location' => 'array',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class);
    }

    public function markedByUser()
    {
        return $this->belongsTo(User::class, 'marked_by');
    }
}
