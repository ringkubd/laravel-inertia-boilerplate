<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InvoiceModificationAddNoteSheet extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->unsignedBigInteger('notesheet_id')->nullable();
            $table->foreign('notesheet_id')->references('id')->on('notesheets');
            $table->integer('page_no')->nullable();
            $table->integer('serial_no')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('notesheet_id');
            $table->dropColumn('page_no');
            $table->dropColumn('serial_no');
        });
    }
}
