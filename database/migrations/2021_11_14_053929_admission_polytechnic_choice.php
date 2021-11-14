<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AdmissionPolytechnicChoice extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admission_polytechnic_choice', function (Blueprint $table){
            $table->unsignedBigInteger('admission_id');
            $table->foreign('admission_id')->references('id')->on('admissions');
            $table->unsignedBigInteger('polytechnic_id');
            $table->foreign('polytechnic_id')->references('id')->on('polytechnics');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admission_polytechnic_choice');
    }
}
