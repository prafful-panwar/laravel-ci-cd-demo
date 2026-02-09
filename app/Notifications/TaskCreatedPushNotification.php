<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TaskCreatedPushNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Task $task) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // Return your Push channel driver here, e.g., 'fcm', 'apn'
        // return ['fcm'];
        return [];
    }

    /**
     * Get the Push representation of the notification.
     */
    public function toPush(object $notifiable): mixed
    {
        // Return Push message object
        // return FcmMessage::create()
        //     ->setData(['id' => $this->task->id])
        //     ->setNotification(\NotificationChannels\Fcm\Resources\Notification::create()
        //         ->setTitle('New Task')
        //         ->setBody('Task created: ' . $this->task->title));
        return null;
    }
}
