<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeacherFaceDataTable extends Migration
{
    public function up()
    {
        Schema::create('teacher_face_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('face_image')->nullable()->comment('Reference face photo path');
            $table->string('face_image_2')->nullable()->comment('Secondary reference face');
            $table->string('face_image_3')->nullable()->comment('Tertiary reference face');
            $table->text('face_encoding')->nullable()->comment('JSON array of face feature vector');
            $table->json('metadata')->nullable()->comment('Camera info, lighting, etc.');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique('user_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('teacher_face_data');
    }
}
