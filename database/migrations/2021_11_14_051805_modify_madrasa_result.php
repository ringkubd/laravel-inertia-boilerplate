<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyMadrasaResult extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('madrasah_results', function (Blueprint $table) {
            $table->string('pass_year')->nullable()->after('ten_gpa');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('madrasah_results', function (Blueprint $table) {
            //
        });
    }
}
