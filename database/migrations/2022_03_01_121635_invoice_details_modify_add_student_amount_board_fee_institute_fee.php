<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InvoiceDetailsModifyAddStudentAmountBoardFeeInstituteFee extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoice_details', function (Blueprint $table) {
            $table->double('student_amount')->after('fee_type')->nullable();
            $table->double('board_amount')->after('student_amount')->nullable();
            $table->double('institute_amount')->after('board_amount')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoice_details', function (Blueprint $table) {
            $table->dropColumn('student_amount');
            $table->dropColumn('board_amount');
            $table->dropColumn('institute_amount');
        });
    }
}
