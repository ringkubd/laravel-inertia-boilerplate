<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('results', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("student_id");
            $table->foreign("student_id")->references('id')->on('students');
            $table->integer("semester");
            $table->float("gpa")->nullable();
            $table->enum('status', ['Passed', 'Failed'])->default("Pass");
            $table->integer("failed_in_subject")->default(0)->nullable();
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
        Schema::dropIfExists('results');
    }
}