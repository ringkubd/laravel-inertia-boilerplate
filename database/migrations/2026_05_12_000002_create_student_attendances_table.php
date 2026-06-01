<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentAttendancesTable extends Migration
{
    public function up()
    {
        Schema::create('student_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('class_room_id')->constrained('class_rooms')->onDelete('cascade');
            $table->date('date');
            $table->enum('status', ['present', 'absent', 'late'])->default('present');
            $table->foreignId('marked_by')->constrained('users')->onDelete('cascade');
            $table->json('location')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['student_id', 'class_room_id', 'date'], 'student_attendance_unique');
        });
    }

    public function down()
    {
        Schema::dropIfExists('student_attendances');
    }
}
