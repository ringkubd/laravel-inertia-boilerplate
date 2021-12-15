<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MailBoxAttachement extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mail_box_attachments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mail_box_id');
            $table->foreign('mail_box_id')->on('mail_boxes')->references('id');
            $table->string('content_type');
            $table->string('content');
            $table->string('type');
            $table->string('img_src')->nullable();
            $table->string('size');
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
        Schema::dropIfExists('mail_box_attachments');
    }
}
