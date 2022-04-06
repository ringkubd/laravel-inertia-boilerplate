<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMobileApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mobile_applications', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('platform', ['android', 'ios'])->default('android');
            $table->string('file_path');
            $table->bigInteger('number_of_download');
            $table->tinyInteger('status')->default(1)->comment('1=>active 2=>pending');
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
        Schema::dropIfExists('mobile_applications');
    }
}
