<?php

namespace App\Observers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

namespace App\Observers;

use App\Models\Task;
use Illuminate\Support\Facades\Storage;

class TaskObserver
{
    public function created(Task $task)
    {
        $this->logDetailed($task, "créée");
    }

    public function updated(Task $task)
    {
        $this->logDetailed($task, "mise à jour");
    }

    public function deleted(Task $task)
    {
        $this->logDetailed($task, "supprimée");
    }

    protected function logDetailed(Task $task, string $actionType)
    {
        $user = $task->createdBy()->first();
        $project = $task->project()->first();
        $priority = $task->priority()->first();
        $column = $task->column()->first();
        $category = $task->category()->first();

        $nomComplet = $user
            ? trim("{$user->firstname} {$user->lastname}")
            : 'utilisateur inconnu';

        $projectName = $project->name ?? 'projet inconnu';
        $priorityName = $priority->name ?? 'non définie';
        $columnName = $column->name ?? 'non définie';
        $categoryName = $category->name ?? 'non définie';

        $entry = '[' . now()->timezone('Europe/Paris')->format('Y-m-d H:i:s') . '] '
            . "Tâche #{$task->id} ({$task->name}) {$actionType} par {$nomComplet} | "
            . "Projet : {$projectName} | "
            . "Statut : {$columnName} | "
            . "Priorité : {$priorityName} | "
            . "Catégorie : {$categoryName}";

        Storage::append('journal-tasks.log', $entry);
    }
}