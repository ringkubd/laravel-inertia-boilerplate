<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bill_attachments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('folder_id');
            $table->bigInteger('student_id');
            $table->integer('semester');
            $table->string('academic_session');
            $table->string('attachment_type');
            $table->string('attachment_link');
            $table->boolean('status')->default(0)->comment('0 => Pending, 1 => Approved, 2 => Rejected');
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
        Schema::dropIfExists('bill_attachments');
    }
}
