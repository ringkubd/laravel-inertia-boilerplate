<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AcademicSessionStartEnd extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('academic_sessions', function (Blueprint $table) {
            $table->date('start_date')->default('CURRENT_TIMESTAMP')->after('session');
            $table->date('end_date')->after('start_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('academic_sessions', function (Blueprint $table) {
            $table->dropIfExists('start_date');
            $table->dropIfExists('end_date');
        });
    }
}
