<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PaymentSlipModiy extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payment_slips', function (Blueprint $table) {
            $table->string('fee_type')->after('semester');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payment_slips', function (Blueprint $table) {
            $table->dropColumn('fee_type');
        });
    }
}
