<?php

namespace App\Models;

use App\Models\Traits\RecordsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notesheet extends Model
{
    use HasFactory, RecordsActivity;

    protected $guarded = ['id'];

    public function invoice(){
        return $this->belongsTo(Invoice::class, 'invoice_id','invoice_id');
    }

    public function student(){
        return $this->belongsTo(Student::class);
    }

}
