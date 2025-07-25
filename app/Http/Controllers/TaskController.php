<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Column;
use App\Models\Priority;
use App\Models\Task;
use Illuminate\Http\Request;
use App\Notifications\CreateTask;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $column_id = $request->column_id;

        $column = Column::find($column_id);

        $columns = Column::where('project_id', $column->project_id)->get();
        $priorities = Priority::where('project_id', $column->project_id)->get();
        $categories = Category::where('project_id', $column->project_id)->get();

        return view('task/create', [
            'id_column' => $column_id,
            'name' => 'Nouvelle tâche',
            'id_project' => $column->project_id,
            'columns' => $columns,
            'priorities' => $priorities,
            'categories' => $categories,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Put the task on last position of the column
        $lastTask = Task::where('column_id', $request->input('column_id'))
            ->orderBy('order', 'desc')
            ->first();
        $order = $lastTask ? $lastTask->order + 1 : 0;
        $request->merge(['order' => $order]);

        // Validate the request data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date',
            'deadline_at' => 'nullable|date',
            'column_id' => 'required|exists:columns,id',
            'priority_id' => 'nullable|exists:priorities,id',
            'category_id' => 'nullable|exists:categories,id',
            'project_id' => 'required|exists:projects,id',
        ]);

        // Create a new task
        $task = Task::create(array_merge($validated, [
            'created_by' => auth()->id(),
        ]));

        foreach($task->project->users()->get() as $user){
            $user->notify(new CreateTask($task));
        }

        return response()->json([
            'message' => 'Tâche ajoutée avec succès',
            'task' => $task,
            'column_id' => $task->column_id,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request)
    {
        $id_task = $request->task_id;
        $task = Task::find($id_task);

        $column = Column::find($task->column_id);
        $columns = Column::where('project_id', $column->project_id)->get();

        $priority = Priority::find($task->priority_id);
        $priorities = Priority::where('project_id', $column->project_id)->get();

        $category = Category::find($task->category_id);
        $categories = Category::where('project_id', $column->project_id)->get();

        return view('task/edit', [
            'task' => $task,
            'id_column' => $column->id,
            'columns' => $columns,
            'priorities' => $priorities,
            'categories' => $categories,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date',
            'deadline_at' => 'nullable|date',
            'column_id' => 'required|exists:columns,id',
            'priority_id' => 'nullable|exists:priorities,id',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $task = Task::find($request->task_id);
        $task->name = $validated['name'];
        $task->description = $validated['description'];
        $task->deadline_at = $validated['deadline_at'];
        $task->column_id = $validated['column_id'];
        $task->priority_id = $validated['priority_id'];
        $task->category_id = $validated['category_id'];
        $task->save();

        return response()->json([
            'message' => 'Tâche mise à jour avec succès',
            'task' => $task,
        ]);
    }

    /**
     * Show the form for deleting the specified resource.
     */
    public function delete(Request $request)
    {
        $id_task = $request->task_id;
        $task = Task::find($id_task);

        return view('task/delete', [
            'task' => $task,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $task = Task::find($request->task_id);

        $task->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tâche supprimée avec succès',
        ]);
    }
}
