<?php

namespace App\Events;

use App\Models\Bug;
use App\Models\BugHistory;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BugHistoryCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public BugHistory $bugHistory;

    /**
     * Create a new event instance.
     */
    public function __construct(BugHistory $bugHistory)
    {
        $this->bugHistory = $bugHistory;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): Channel
    {
        return new Channel('bugsHistory');
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->bugHistory->id,
            'title' => $this->bugHistory->title,
            'created_at' => $this->bug->created_at
        ];
    }
}
