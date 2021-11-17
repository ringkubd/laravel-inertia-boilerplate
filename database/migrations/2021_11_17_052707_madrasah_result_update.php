<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MadrasahResultUpdate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('madrasah_results', function (Blueprint $table) {
            $table->unsignedBigInteger('added_by')->nullable()->change();
            $table->unsignedBigInteger('approved_by')->nullable()->change();
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
