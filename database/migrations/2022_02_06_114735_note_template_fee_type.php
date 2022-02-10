<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class NoteTemplateFeeType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notesheet_template_fee_type', function (Blueprint $table) {
            $table->unsignedBigInteger('notesheet_template_id');
            $table->unsignedBigInteger('fee_type_id');
            $table->foreign('notesheet_template_id')->on('notesheet_templates')->references('id');
            $table->foreign('fee_type_id')->on('fee_types')->references('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notesheet_template_fee_type');
    }
}
