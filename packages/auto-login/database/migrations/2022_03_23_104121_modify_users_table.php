<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(config('autologin.users_table', 'users'), function (Blueprint $table) {
            $table->string('app_token');
            $table->string('app_reference');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropColumns(config('autologin.users_table', 'users'), ['app_token', 'app_reference']);
    }
};
