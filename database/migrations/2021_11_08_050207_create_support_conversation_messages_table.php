<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupportConversationMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('support_conversation_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sender');
            $table->foreign('sender')->references('id')->on('users');
            $table->unsignedBigInteger('support_conversation_id');
            $table->foreign('support_conversation_id')->on('support_conversations')->references('id');
            $table->string('message', 1000)->nullable();
            $table->string('attachment', 500)->nullable();
            $table->string('attachment_type')->nullable();
            $table->timestamp('seen_at')->default(now())->nullable();
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
        Schema::dropIfExists('support_conversation_messages');
    }
}
