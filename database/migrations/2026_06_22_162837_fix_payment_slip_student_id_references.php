<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Convert payment_slips where student_id accidentally stores students.id (PK)
        // instead of students.student_id (the cross-reference key)
        DB::statement("
            UPDATE payment_slips ps
            JOIN students s ON ps.student_id = s.id
            SET ps.student_id = s.student_id
            WHERE ps.student_id != s.student_id
        ");
    }

    public function down()
    {
        // Cannot revert - data is already corrected
    }
};
