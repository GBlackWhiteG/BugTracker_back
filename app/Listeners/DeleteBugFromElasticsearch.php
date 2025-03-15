<?php

namespace App\Listeners;

use App\Events\BugDeleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;

class DeleteBugFromElasticsearch
{

    /**
     * Handle the event.
     */
    public function handle(BugDeleted $event): void
    {
        $bug = $event->bug;
        $host = config('database.connections.elasticsearch.host');

        Http::delete("$host/bugs/_doc/{$bug->id}");
    }
}
