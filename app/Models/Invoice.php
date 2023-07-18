<?php

namespace App\Models;

use App\Models\Traits\RecordsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function getStudentNameAttribute($value){
        return ucwords(strtolower($value));
    }

    public function student(){
        return $this->belongsTo(Student::class)->orderBy('ssc_roll');
    }

    public function details(){
        return $this->hasMany(InvoiceDetail::class)->orderBy('student_id');
    }

    public function paymentSlip(){
        return $this->belongsToMany(PaymentSlip::class, 'students','id', 'student_id', 'student_id', 'student_id');
    }
}
