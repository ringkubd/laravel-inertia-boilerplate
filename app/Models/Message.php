<?php

namespace App\Models;

use App\Models\Traits\RecordsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory, RecordsActivity;

    protected $guarded = ['id'];

    public function conversation(){
        return $this->belongsTo(Conversation::class);
    }

    public function sender(){
        return $this->hasOne(User::class, 'id', 'sender');
    }

}
