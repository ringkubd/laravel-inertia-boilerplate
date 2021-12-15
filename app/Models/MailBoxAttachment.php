<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MailBoxAttachment extends Model
{
    use HasFactory;

    protected $table = 'mail_box_attachments';

    protected $guarded = ['id'];
}
