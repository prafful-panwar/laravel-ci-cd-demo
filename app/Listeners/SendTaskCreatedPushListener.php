<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\TaskCreated;
use App\Notifications\TaskCreatedPushNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

/**
 * Listener that sends push notifications when a task is created.
 *
 * This is currently a placeholder implementation that logs the event.
 * In production, this would integrate with a push notification service (e.g., FCM, APNs).
 *
 * This listener is queued and will retry up to 3 times with exponential backoff
 * (10s, 30s, 60s) if the push notification fails to send.
 *
 * @see \App\Events\TaskCreated
 * @see \App\Notifications\TaskCreatedPushNotification
 */
class SendTaskCreatedPushListener implements ShouldQueue
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
     * Currently logs the task creation event. In production, this would
     * send a push notification via a service like FCM or APNs.
     *
     * @param  TaskCreated  $event  The task created event instance
     */
    public function handle(TaskCreated $event): void
    {
        // Placeholder implementation
        Log::info('Sending Push Notification for Task Created event', ['task_id' => $event->task->id]);

        // In real implementation:
        // Notification::route('fcm', 'token')
        //     ->notify(new TaskCreatedPushNotification($event->task));
    }
}
