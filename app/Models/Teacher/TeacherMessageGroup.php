<?php

namespace App\Models\Teacher;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TeacherMessageGroup extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function members(){
        return $this->belongsToMany(User::class, 'teacher_message_group_members');
    }

    public function messages(){
        return $this->hasMany(TeacherMessage::class, 'conversation_id', 'conversation_id');
    }
}
