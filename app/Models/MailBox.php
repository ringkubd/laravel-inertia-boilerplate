<?php

namespace App\Models;

use DOMDocument;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MailBox extends Model
{
    use HasFactory;

    protected static function booted()
    {
        $user_mail = auth()->user()?->email;
        static::addGlobalScope('relation', function (Builder $builder)use($user_mail){
            if (auth()->user()->hasRole('student')) {
                $builder->where('to', auth()->user()->email);
            }
            $builder->with(['attachments']);
        });

    }

    protected $guarded = ['id'];


    public function attachments(){
        return $this->hasMany(MailBoxAttachment::class, 'mail_box_id');
    }
}
