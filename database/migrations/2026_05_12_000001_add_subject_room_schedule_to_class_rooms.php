<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSubjectRoomScheduleToClassRooms extends Migration
{
    public function up()
    {
        Schema::table('class_rooms', function (Blueprint $table) {
            if (!Schema::hasColumn('class_rooms', 'subject')) {
                $table->string('subject')->nullable()->after('name');
            }
            if (!Schema::hasColumn('class_rooms', 'room')) {
                $table->string('room')->nullable()->after('teacher_id');
            }
            if (!Schema::hasColumn('class_rooms', 'schedule')) {
                $table->json('schedule')->nullable()->after('room');
            }
        });
    }

    public function down()
    {
        Schema::table('class_rooms', function (Blueprint $table) {
            $table->dropColumn(['subject', 'room', 'schedule']);
        });
    }
}
