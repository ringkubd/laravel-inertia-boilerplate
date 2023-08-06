<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('sl_no')->unique()->index();
            $table->string('unit');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('category_id')->index();
            $table->unsignedBigInteger('brand_id')->nullable()->index();
            $table->unsignedBigInteger('class_room_id')->nullable()->index();
            $table->string('lesson')->nullable()->index();
            $table->tinyInteger('status')->default(1)->comment('1 => active, 0 => inactive');
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
        Schema::dropIfExists('products');
    }
}
