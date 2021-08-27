<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMadrashasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('madrashas', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('district');
            $table->string('address');
            $table->string('telephone')->nullable();
            $table->string('mobile');
            $table->string('email');
            $table->string('fax')->nullable();
            $table->string('principal_id')->nullable();
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
        Schema::dropIfExists('madrashas');
    }
}
