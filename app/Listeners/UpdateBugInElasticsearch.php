<?php

namespace App\Listeners;

use App\Events\BugUpdated;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;

class UpdateBugInElasticsearch
{

    /**
     * Handle the event.
     */
    public function handle(BugUpdated $event): void
    {
        $bug = $event->bug;
        $host = env('ELASTICSEARCH_HOST');

        Http::put("$host/bugs/_doc/{$bug->id}", [
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
