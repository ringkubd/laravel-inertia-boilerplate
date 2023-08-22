<?php

namespace App\Models\Teacher;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TeacherMessage extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function attachments(){
        return $this->hasMany(TeacherMessageAttachment::class);
    }

    public function seenBy(){
        return $this->hasMany(TeacherMessageSeen::class);
    }

    public function sendBy(){
        return $this->belongsTo(User::class, 'from', 'id');
    }
}
