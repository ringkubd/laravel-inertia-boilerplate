<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MadrasahResult extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('madrasah_results', function (Blueprint $table) {
            $table->boolean('approved')->after('status')->default(0);
            $table->unsignedBigInteger('added_by')->after('approved')->nullable();
            $table->foreign('added_by')->on('users')->references('id');
            $table->unsignedBigInteger('approved_by')->after('added_by')->nullable();
            $table->foreign('approved_by')->on('users')->references('id');
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
