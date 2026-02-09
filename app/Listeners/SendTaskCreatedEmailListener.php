<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\TaskCreated;
use App\Notifications\TaskCreatedEmailNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

/**
 * Listener that sends email notifications when a task is created.
 *
 * This listener is queued and will retry up to 3 times with exponential backoff
 * (10s, 30s, 60s) if the email fails to send.
 *
 * @see \App\Events\TaskCreated
 * @see \App\Notifications\TaskCreatedEmailNotification
 */
class SendTaskCreatedEmailListener implements ShouldQueue
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
     *
     * Sends an email notification to the configured admin email address
     * if the 'task_notification_email' config value is set.
     *
     * @param  TaskCreated  $event  The task created event instance
     */
    public function handle(TaskCreated $event): void
    {
        $email = config('app.task_notification_email');

        if ($email) {
            Notification::route('mail', $email)
                ->notify(new TaskCreatedEmailNotification($event->task));
        }
    }
}
