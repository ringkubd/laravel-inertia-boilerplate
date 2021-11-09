<?php

namespace App\Events;

use App\Http\Resources\SupportMessageResource;
use App\Models\SupportConversation;
use App\Models\SupportConversationMessage;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SupportEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $conversation;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(SupportMessageResource $conversation)
    {
        $this->conversation = $conversation;
        $this->dontBroadcastToCurrentUser();
    }

    /**
     * Get the channels the event should broadcast on..
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('support.'.$this->conversation->support_conversation_id);
    }
}
