<?php

namespace App\Events;

use App\Models\TicketMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketMessageCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $ticketId;

    public function __construct(TicketMessage $message)
    {
        $this->message = $message;
        $this->ticketId = $message->support_ticket_id;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('ticket.' . $this->ticketId);
    }

    public function broadcastAs()
    {
        return 'TicketMessageCreated';
    }

    public function broadcastWith()
    {
        return [
            'message' => $this->message->load(['user', 'attachments']),
            'ticketId' => $this->ticketId,
        ];
    }
}
