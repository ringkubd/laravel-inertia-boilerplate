<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyNoteSheet extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notesheets', function (Blueprint $table) {
            $table->unsignedBigInteger('invoice_id')->after('serial_no');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notesheet', function (Blueprint $table) {
            $table->dropColumn('invoice_id');
        });
    }
}
