<?php

namespace App\Models;

use App\Models\Traits\RecordsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = ['id'];

    public function getStudentNameAttribute($value){
        return ucwords(strtolower($value));
    }

    public function student(){
        return $this->belongsTo(Student::class)->orderBy('polytechnic_roll');
    }

    public function details(){
        return $this->hasMany(InvoiceDetail::class);
    }

    public function paymentSlip(){
        return $this->hasMany(PaymentSlip::class, 'student_id', 'student_id');
    }

    public function notesheets(){
        return $this->hasMany(Notesheet::class, 'invoice_id', 'invoice_id');
    }
}
