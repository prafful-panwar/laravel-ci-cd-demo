<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Email notification sent when a new task is created.
 *
 * This notification is sent to the configured admin email address
 * (defined in config/app.php as 'task_notification_email').
 * It includes task details and a link to view the task.
 */
class TaskCreatedEmailNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @param  Task  $task  The task that was created
     */
    public function __construct(public Task $task) {}

    /**
     * Get the notification's delivery channels.
     *
     * @param  object  $notifiable  The notifiable entity (typically an AnonymousNotifiable)
     * @return array<int, string> Array of notification channels (e.g., ['mail'])
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * Builds an email message with task details including:
     * - Task title and description
     * - Task status (human-readable label)
     * - Link to view the task
     *
     * @param  object  $notifiable  The notifiable entity
     * @return MailMessage The mail message instance
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Task Created: '.$this->task->title)
            ->line('A new task has been created.')
            ->line('Title: '.$this->task->title)
            ->line('Status: '.$this->task->status->value)
            ->action('View Task', url('/tasks/'.$this->task->id))
            ->line('Thank you for using our application!');
    }
}
