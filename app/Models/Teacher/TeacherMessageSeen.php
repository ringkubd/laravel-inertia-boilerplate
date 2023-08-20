<?php

namespace App\Models\Teacher;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TeacherMessageSeen extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];
}
