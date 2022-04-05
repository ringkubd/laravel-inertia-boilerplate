<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PaymentSlipModiyAddStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payment_slips', function (Blueprint $table) {
            $table->tinyInteger('status')->default(0)->after('added_by')->comment('0=>pending, 1=>approved, 2 => rejected');
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
            $table->dropColumn('status');
        });
    }
}
