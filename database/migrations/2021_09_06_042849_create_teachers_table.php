<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeachersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('users_id');
            $table->string('name');
            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('mobile');
            $table->date('joining_date');
            $table->string('designation');
            $table->unsignedBigInteger('trade_id')->nullable();
            $table->unsignedBigInteger('madrashas_id')->nullable();
            $table->date('dob');
            $table->string('photo');
            $table->bigInteger('nid')->nullable();
            $table->bigInteger('bank_account');
            $table->string('bank_branch');
            $table->string('bank_name')->default("Islami Bank");
            $table->text('present_address');
            $table->text('permanent_address');
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
        Schema::dropIfExists('teachers');
    }
}
