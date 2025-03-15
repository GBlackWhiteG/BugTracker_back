<?php

namespace App\Listeners;

use App\Events\BugCreated;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class SendBugToElasticsearch
{

    /**
     * Handle the event.
     */
    public function handle(BugCreated $event): void
    {
        $bug = $event->bug;
        $host = config('database.connections.elasticsearch.host');

        Http::post("$host/bugs/_doc/{$bug->id}", [
            'title' => $bug->title,
            'description' => $bug->description,
            'steps' => $bug->steps,
            'priority' => $bug->priority,
            'status' => $bug->status,
            'criticality' => $bug->criticality,
            'responsible_user_id' => $bug->responsible_user_id,
            'created_at' => $bug->created_at,
            'updated_at' => $bug->updated_at,
        ]);
    }
}
