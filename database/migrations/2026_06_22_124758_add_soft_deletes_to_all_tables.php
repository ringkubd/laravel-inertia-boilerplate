<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        $tables = [
            'activities', 'bill_attachments', 'bteb_results', 'class_room_students',
            'conversations', 'mail_box_attachments', 'mail_boxes', 'messages',
            'mobile_applications', 'payment_slip_attachments',
            'teacher_attendances', 'teacher_face_data',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && !Schema::hasColumn($table, 'deleted_at')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->softDeletes();
                });
            }
        }
    }

    public function down()
    {
        $tables = [
            'activities', 'bill_attachments', 'bteb_results', 'class_room_students',
            'conversations', 'mail_box_attachments', 'mail_boxes', 'messages',
            'mobile_applications', 'payment_slip_attachments',
            'teacher_attendances', 'teacher_face_data',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'deleted_at')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->dropColumn('deleted_at');
                });
            }
        }
    }
};
