<?php

namespace App\Providers;

use App\Events\BugCreated;
use App\Events\BugDeleted;
use App\Events\BugHistoryCreated;
use App\Events\BugUpdated;
use App\Listeners\DeleteBugFromElasticsearch;
use App\Listeners\SendBugToElasticsearch;
use App\Listeners\UpdateBugInElasticsearch;
use App\Notifications\VerifyEmail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [

        ],
        BugCreated::class => [
            SendBugToElasticsearch::class,
        ],
        BugUpdated::class => [
            UpdateBugInElasticsearch::class,
        ],
        BugDeleted::class => [
            DeleteBugFromElasticsearch::class,
        ],

        BugHistoryCreated::class => [

        ]
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        Event::listen(Registered::class, function ($event) {
            $event->user->notify(new VerifyEmail());
        });
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
