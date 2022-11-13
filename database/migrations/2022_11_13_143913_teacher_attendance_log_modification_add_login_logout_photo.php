<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TeacherAttendanceLogModificationAddLoginLogoutPhoto extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('teacher_attendance_logs', function (Blueprint $table) {
            $table->string('login_photo')->after('login_location');
            $table->string('logout_photo')->after('logout_location');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('teacher_attendance_logs', function (Blueprint $table) {
            $table->dropColumn('login_photo');
            $table->dropColumn('logout_photo');
        });
    }
}
