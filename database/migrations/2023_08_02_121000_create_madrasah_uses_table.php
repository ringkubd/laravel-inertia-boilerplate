<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMadrasahUsesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('madrasah_uses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('madrasha_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('product_id')->index();
            $table->unsignedBigInteger('class_room_id')->index();
            $table->string('academic_session')->index();
            $table->double('quantity', 10,2);
            $table->date('uses_date')->index();
            $table->text('note')->nullable();
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
        Schema::dropIfExists('madrasah_uses');
    }
}
