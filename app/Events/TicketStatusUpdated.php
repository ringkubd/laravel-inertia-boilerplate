<?php

namespace App\Events;

use App\Models\SupportTicket;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketStatusUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $ticket;
    public $oldStatus;

    public function __construct(SupportTicket $ticket, $oldStatus)
    {
        $this->ticket = $ticket;
        $this->oldStatus = $oldStatus;
    }

    public function broadcastOn()
    {
        return [
            new PrivateChannel('ticket.' . $this->ticket->id),
            new Channel('support-tickets'),
        ];
    }

    public function broadcastAs()
    {
        return 'TicketStatusUpdated';
    }

    public function broadcastWith()
    {
        return [
            'ticket' => $this->ticket->load(['user', 'assignedTo']),
            'oldStatus' => $this->oldStatus,
        ];
    }
}
