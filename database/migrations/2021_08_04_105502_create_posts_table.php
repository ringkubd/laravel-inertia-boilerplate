<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('meta_title');
            $table->string('slug')->unique()->index();
            $table->datetime('published_at');
            $table->enum('post_type', ['post', 'page', 'video']);
            $table->string('summary', 500)->nullable();
            $table->text('content')->nullable();
            $table->string('thumbnail')->nullable();
            $table->unsignedBigInteger('author');
            $table->enum('post_status', ['active', 'draft']);
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
        Schema::dropIfExists('posts');
    }
}
