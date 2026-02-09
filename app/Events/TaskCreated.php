<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Task;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event fired when a new task is created.
 *
 * This event is dispatched by the TaskService after successfully creating a task.
 * It triggers multiple listeners for sending notifications via email, SMS, and push.
 *
 * @see \App\Listeners\SendTaskCreatedEmailListener
 * @see \App\Listeners\SendTaskCreatedSmsListener
 * @see \App\Listeners\SendTaskCreatedPushListener
 */
class TaskCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public Task $task) {}
}
