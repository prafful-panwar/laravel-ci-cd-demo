<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TaskCreatedSmsNotification extends Notification
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
        // Return your SMS channel driver here, e.g., 'nexmo', 'twilio'
        // return ['nexmo'];
        return [];
    }

    /**
     * Get the SMS representation of the notification.
     */
    public function toSms(object $notifiable): mixed
    {
        // Return SMS message object
        // return (new BoxSmsMessage)
        //     ->content('New task created: ' . $this->task->title);
        return null;
    }
}
