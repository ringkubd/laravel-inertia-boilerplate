<?php

namespace App\Models;

use App\Models\Traits\RecordsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory, RecordsActivity;

    protected $guarded = ['id'];

    public function student(){
        return $this->belongsTo(Student::class);
    }

    public function details(){
        return $this->hasMany(InvoiceDetail::class);
    }
}
