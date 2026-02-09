<?php

declare(strict_types=1);

namespace App\Providers;

use App\Events\TaskCreated;
use App\Listeners\SendTaskCreatedEmailListener;
use App\Listeners\SendTaskCreatedPushListener;
use App\Listeners\SendTaskCreatedSmsListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class TaskEventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        TaskCreated::class => [
            SendTaskCreatedEmailListener::class,
            SendTaskCreatedSmsListener::class,
            SendTaskCreatedPushListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
