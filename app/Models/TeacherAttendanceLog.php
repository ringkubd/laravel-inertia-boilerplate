<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TeacherAttendanceLog extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'face_match_score' => 'decimal:2',
        'face_verified' => 'boolean',
        'location_verified' => 'boolean',
        'location_distance' => 'decimal:2',
        'device_info' => 'array',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
