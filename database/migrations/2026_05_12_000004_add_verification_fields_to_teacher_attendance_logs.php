<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVerificationFieldsToTeacherAttendanceLogs extends Migration
{
    public function up()
    {
        Schema::table('teacher_attendance_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('teacher_attendance_logs', 'face_match_score')) {
                $table->decimal('face_match_score', 5, 2)->nullable()->after('logout_photo')
                      ->comment('Percentage match with enrolled face (0-100)');
            }
            if (!Schema::hasColumn('teacher_attendance_logs', 'face_verified')) {
                $table->boolean('face_verified')->default(false)->after('face_match_score');
            }
            if (!Schema::hasColumn('teacher_attendance_logs', 'location_verified')) {
                $table->boolean('location_verified')->default(false)->after('face_verified');
            }
            if (!Schema::hasColumn('teacher_attendance_logs', 'location_distance')) {
                $table->decimal('location_distance', 8, 2)->nullable()->after('location_verified')
                      ->comment('Distance in meters from madrasah');
            }
            if (!Schema::hasColumn('teacher_attendance_logs', 'device_info')) {
                $table->json('device_info')->nullable()->after('location_distance');
            }
        });
    }

    public function down()
    {
        Schema::table('teacher_attendance_logs', function (Blueprint $table) {
            $table->dropColumn([
                'face_match_score',
                'face_verified',
                'location_verified',
                'location_distance',
                'device_info',
            ]);
        });
    }
}
