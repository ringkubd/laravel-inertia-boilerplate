<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateFeeTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fee_types', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->boolean("is_madrasa")->default(1)->comment("1 => madrasa, 0=>polytechnic");
            $table->softDeletes();
            $table->timestamps();
        });
        DB::table('users')->insert([
                [
                    'name' => 'Semester Fee',
                    'is_madrasa' => false,
                ],
                [
                    'name' => 'Exam Fee',
                    'is_madrasa' => false,
                ],
                [
                    'name' => 'MMA',
                    'is_madrasa' => false,
                ]
            ]
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fee_types');
    }
}
