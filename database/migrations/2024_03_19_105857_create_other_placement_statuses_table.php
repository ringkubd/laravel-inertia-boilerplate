<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOtherPlacementStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('other_placement_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Student::class)->index()->references('id')->on('students');
            $table->text('note')->nullable();
            $table->foreignIdFor(\App\Models\User::class, 'added_by')->index()->references('id')->on('users');
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
        Schema::dropIfExists('other_placement_statuses');
    }
}
