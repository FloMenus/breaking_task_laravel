<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Task;
use Carbon\Carbon;

class CreateTask extends Notification
{
    use Queueable;
    private Task $task;

    /**
     * Create a new notification instance.
     */
    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Nouvelle tâche : ' . $this->task->name)
            ->line('Une nouvelle tâche a été créée dans le projet ' . $this->task->project->name)
            ->line('Nom de la tâche : ' . $this->task->name)
            ->action('Voir la tâche', url('/project/' . $this->task->project->id));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'task_id' => $this->task->id,
            'title' => 'Nouvelle tâche : ' . $this->task->name,
            'description' => 
                'Une nouvelle tâche a été créée dans le projet "' . $this->task->project->name . '".' . "\n" .
                'Nom de la tâche : ' . $this->task->name,
            'deadline' => $this->task->deadline_at ? Carbon::parse($this->task->deadline_at)->format('d/m/Y') : null,
            'project_id' => $this->task->project->id,
        ];
    }
}