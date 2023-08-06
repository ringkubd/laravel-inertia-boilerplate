<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('madrasha_id')->index();
            $table->dateTime('sending_at');
            $table->dateTime('receiving_at')->nullable();
            $table->string('courier_name')->nullable();
            $table->unsignedBigInteger('sending_by')->index();
            $table->unsignedBigInteger('receiving_by')->nullable()->index();
            $table->text('sending_notes')->nullable();
            $table->text('receiving_notes')->nullable();
            $table->enum('status', ['Pending', 'Arrived', 'Received'])->default('Pending');
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
        Schema::dropIfExists('transactions');
    }
}
