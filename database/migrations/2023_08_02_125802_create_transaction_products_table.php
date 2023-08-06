<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transaction_id')->index();
            $table->unsignedBigInteger('product_id')->index();
            $table->double('sending_quantity', 10, 2);
            $table->double('receiving_quantity', 10, 2)->nullable();
            $table->text('sending_notes')->nullable();
            $table->text('receiving_notes')->nullable();
            $table->tinyInteger('checked_and_store')->default(0)->comment('0=> not_checked, 1 => checked_not_stored 2 => checked_and_stored');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaction_products');
    }
}
