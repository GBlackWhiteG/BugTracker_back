<?php

namespace App\Events;

use App\Models\Bug;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BugUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Bug $bug;

    /**
     * Create a new event instance.
     */
    public function __construct(Bug $bug)
    {
        $this->bug = $bug;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
