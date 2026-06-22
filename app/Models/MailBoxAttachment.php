<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MailBoxAttachment extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'mail_box_attachments';

    protected $guarded = ['id'];
}
