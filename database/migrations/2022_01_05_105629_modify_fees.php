<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyFees extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fees', function (Blueprint $table) {
            $table->double('student')->default(0)->nullable()->after('fee_type');
            $table->double('board')->default(0)->nullable()->after('fee_type');
            $table->double('institute')->default(0)->nullable()->after('fee_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fees', function (Blueprint $table) {
            $table->dropColumn('student');
            $table->dropColumn('board');
            $table->dropColumn('institute');
        });
    }
}
