<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AdmissionTradeChoice extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admission_trade_choice', function (Blueprint $table){
            $table->unsignedBigInteger('admission_id');
            $table->foreign('admission_id')->references('id')->on('admissions');
            $table->unsignedBigInteger('trade_id');
            $table->foreign('trade_id')->references('id')->on('trades');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admission_trade_choice');
    }
}
