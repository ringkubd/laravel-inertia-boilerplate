<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentsInformationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('users_id')->nullable();
            $table->string('name');
            $table->integer('class_roll')->nullable();
            $table->integer('student_id')->nullable();
            $table->string('mobile')->nullable();
            $table->string('mobile_1')->nullable();
            $table->string('guardian_mobile')->nullable();
            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('trade')->nullable();
            $table->string('madrasa_trade_id')->nullable();
            $table->string('polytechnic_trade_id')->nullable();
            $table->unsignedBigInteger('madrasha_id')->nullable();
            $table->integer('ssc_roll')->nullable();
            $table->integer('ssc_registration')->nullable();
            $table->string('ssc_session')->nullable();
            $table->integer('polytechnic')->nullable();
            $table->integer('polytechnic_registration')->nullable();
            $table->integer('polytechnic_roll')->nullable();
            $table->string('polytechnic_session')->nullable();
            $table->integer('semester')->nullable();
            $table->string('current_session')->nullable()->comment("2020-2021");
            $table->date('dob')->nullable();
            $table->string('photo')->nullable();
            $table->string('id_card')->nullable();
            $table->bigInteger('nid')->nullable();
            $table->integer('bank_account')->nullable();
            $table->string('bank_branch')->nullable();
            $table->string('bank_name')->default("Islami Bank")->nullable();
            $table->text('present_address')->nullable();
            $table->text('permanent_address')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('students_information');
    }
}
