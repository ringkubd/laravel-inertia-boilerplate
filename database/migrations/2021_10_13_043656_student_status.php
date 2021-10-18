<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class StudentStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->boolean('madrasa_completed')->default(0);
            $table->boolean('polytechnic_completed')->default(0);
            $table->boolean('status')->default(1)->comment("1 => continue, 0 => discontinue, 2 => suspended");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropIfExists('madrasa_completed');
            $table->dropIfExists('polytechnic_completed');
            $table->dropIfExists('status');
        });
    }
}
