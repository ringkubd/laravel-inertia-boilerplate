<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBtebResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bteb_results', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('student_id')->nullable();
            $table->string('exam_year');
            $table->string('technology_name');
            $table->string('roll');
            $table->string('registration_no');
            $table->string('session');
            $table->string('name');
            $table->string('father_name');
            $table->string('mother_name');
            $table->string('institute_name');
            $table->string('result');
            $table->string('CGPA')->nullable();
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
        Schema::dropIfExists('bteb_results');
    }
}
