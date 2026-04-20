<?php

namespace App\Models;

use App\Models\Traits\RecordsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notesheet extends Model
{
    use HasFactory, RecordsActivity;

    protected $fillable = [
        'page_no',
        'serial_no',
        'invoice_id',
        'note_text',
        'user_id',
        'student_id',
        'notesheet_category',
        'meta_data',
    ];

    protected $casts = [
        'meta_data' => 'array',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id', 'invoice_id')->withDefault();
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function scopePolytechnic($query)
    {
        return $query->where('notesheet_category', 'polytechnic');
    }

    public function scopeMadrasah($query)
    {
        return $query->where('notesheet_category', '!=', 'polytechnic');
    }
}
