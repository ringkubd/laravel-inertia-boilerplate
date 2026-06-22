<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Convert payment_slips where student_id stores students.student_id
        // to use students.id (PK) instead, matching all other tables
        DB::statement("
            UPDATE payment_slips ps
            JOIN students s ON ps.student_id = s.student_id
            SET ps.student_id = s.id
            WHERE ps.student_id != s.id
        ");
    }

    public function down()
    {
        // Revert: restore students.student_id references
        DB::statement("
            UPDATE payment_slips ps
            JOIN students s ON ps.student_id = s.id
            SET ps.student_id = s.student_id
            WHERE s.student_id != s.id
        ");
    }
};
