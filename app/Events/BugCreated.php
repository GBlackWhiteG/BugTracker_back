<?php

namespace App\Events;

use App\Models\Bug;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BugCreated implements ShouldBroadcastNow
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
    public function broadcastOn(): Channel
    {
        return new Channel('bugs');
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->bug->id,
            'title' => $this->bug->title,
            'description' => $this->bug->description,
            'status' => $this->bug->status,
            'priority' => $this->bug->priority,
            'criticality' => $this->bug->criticality,
            'created_at' => $this->bug->created_at
        ];
    }
}
