<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupportConversation extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected static function booted() {
        static::addGlobalScope('relation', function (Builder $builder){
            $builder->with('creator', 'message');
        });
    }

    public function creator(){
        return $this->belongsTo(User::class, 'creator');
    }

    public function message(){
        return $this->hasMany(SupportConversationMessage::class, 'support_conversation_id');
    }
}
