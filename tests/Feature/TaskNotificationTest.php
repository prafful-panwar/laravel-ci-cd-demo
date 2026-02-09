<?php

use App\DTOs\Task\CreateTaskDTO;
use App\Enums\TaskStatus;
use App\Events\TaskCreated;
use App\Listeners\SendTaskCreatedEmailListener;
use App\Listeners\SendTaskCreatedPushListener;
use App\Listeners\SendTaskCreatedSmsListener;
use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskCreatedEmailNotification;
use App\Services\TaskService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class TaskNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_task_created_event_is_dispatched_via_service()
    {
        Event::fake([TaskCreated::class]);

        $dto = new CreateTaskDTO(
            title: 'New Task',
            description: 'Description',
            status: TaskStatus::Pending,
            dueDate: now()->addDay()
        );

        $service = app(TaskService::class);
        $service->createTask($dto);

        Event::assertDispatched(TaskCreated::class);
    }

    public function test_task_creation_triggers_events_and_listeners_via_api()
    {
        Event::fake();

        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/tasks', [
            'title' => 'New Task',
            'status' => 'pending',
        ]);

        $response->assertCreated();

        Event::assertDispatched(TaskCreated::class);

        Event::assertListening(
            TaskCreated::class,
            SendTaskCreatedEmailListener::class
        );
        Event::assertListening(
            TaskCreated::class,
            SendTaskCreatedSmsListener::class
        );
        Event::assertListening(
            TaskCreated::class,
            SendTaskCreatedPushListener::class
        );
    }

    public function test_email_listener_supposed_to_be_queued()
    {
        $listener = new SendTaskCreatedEmailListener;
        $this->assertTrue($listener instanceof \Illuminate\Contracts\Queue\ShouldQueue);
    }

    public function test_sms_listener_supposed_to_be_queued()
    {
        $listener = new SendTaskCreatedSmsListener;
        $this->assertTrue($listener instanceof \Illuminate\Contracts\Queue\ShouldQueue);
    }

    public function test_push_listener_supposed_to_be_queued()
    {
        $listener = new SendTaskCreatedPushListener;
        $this->assertTrue($listener instanceof \Illuminate\Contracts\Queue\ShouldQueue);
    }

    public function test_email_listener_sends_notification()
    {
        Notification::fake();
        config(['app.task_notification_email' => 'admin@example.com']);

        $task = Task::factory()->create();
        $event = new TaskCreated($task);
        $listener = new SendTaskCreatedEmailListener;

        $listener->handle($event);

        Notification::assertSentOnDemand(
            TaskCreatedEmailNotification::class,
            function ($notification, $channels, $notifiable) use ($task) {
                // Check if the notifiable is an AnonymousNotifiable and has the correct email
                $route = null;
                if (method_exists($notifiable, 'routeNotificationFor')) {
                    $route = $notifiable->routeNotificationFor('mail');
                } elseif (isset($notifiable->routes['mail'])) {
                    $route = $notifiable->routes['mail'];
                }

                return $route === 'admin@example.com' &&
                    $notification->task->id === $task->id;
            }
        );
    }
}
