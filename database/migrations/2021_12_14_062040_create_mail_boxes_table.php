<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMailBoxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mail_boxes', function (Blueprint $table) {
            $table->id();
            $table->string('to');
            $table->string('sender');
            $table->string('replay_to')->nullable();
            $table->string('cc')->nullable();
            $table->string('bcc')->nullable();
            $table->string('from');
            $table->string('folder');
            $table->string('subject');
            $table->text('html_body');
            $table->text('text_body');
            $table->timestamp('seen_at')->nullable();
            $table->timestamp('received_at')->nullable();
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
        Schema::dropIfExists('mail_boxes');
    }
}
