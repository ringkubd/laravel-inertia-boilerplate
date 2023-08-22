<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TeacherMessageGroupSingleDevide extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('teacher_messages', function (Blueprint $table) {
            $table->enum('type', ['Single', 'Group'])->default('Single')->after('message');
            $table->unsignedBigInteger('to')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('teacher_messages', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
}
