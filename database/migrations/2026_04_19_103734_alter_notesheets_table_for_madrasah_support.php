<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterNotesheetsTableForMadrasahSupport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notesheets', function (Blueprint $table) {
            $table->unsignedBigInteger('invoice_id')->nullable()->change();
            $table->string('notesheet_category')->default('polytechnic')->after('invoice_id');
            $table->json('meta_data')->nullable()->after('notesheet_category');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notesheets', function (Blueprint $table) {
            $table->dropColumn('meta_data');
            $table->dropColumn('notesheet_category');
            $table->unsignedBigInteger('invoice_id')->nullable(false)->change();
        });
    }
}
