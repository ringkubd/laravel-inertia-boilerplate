<?php

namespace App\Models;

use App\Models\Traits\RecordsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceDetail extends Model
{
    use HasFactory;

    protected  $table = "invoice_details";

    protected $guarded = ['id'];

    public function invoice(){
        return $this->belongsTo(Invoice::class);
    }

    public function student(){
        return $this->belongsTo(Student::class);
    }

}
