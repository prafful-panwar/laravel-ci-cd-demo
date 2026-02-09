<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\TaskCreated;
use App\Notifications\TaskCreatedSmsNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendTaskCreatedSmsListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * The number of times the queued listener may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds to wait before retrying the queued listener.
     *
     * @return array<int, int>
     */
    public function backoff(): array
    {
        return [10, 30, 60];
    }

    /**
     * Handle the event.
     */
    public function handle(TaskCreated $event): void
    {
        // Placeholder implementation
        Log::info('Sending SMS for Task Created event', ['task_id' => $event->task->id]);

        // In real implementation:
        // Notification::route('nexmo', '555555555')
        //     ->notify(new TaskCreatedSmsNotification($event->task));
    }
}
