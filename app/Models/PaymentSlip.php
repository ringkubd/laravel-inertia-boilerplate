<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentSlip extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = ['id'];

    public function student(){
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    public function attachments(){
        return $this->hasMany(PaymentSlipAttachment::class);
    }
}
