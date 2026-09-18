<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::with([
            'activity',
            'assignee',
            'creator',
        ])
            ->latest()
            ->get();

        return Inertia::render(
            'Tasks/Index',
            [
                'tasks' => $tasks,
            ]
        );
    }

    public function create(Request $request)
    {
        return Inertia::render(
            'Tasks/Create',
            [
                'activities' =>
                    Activity::orderBy('name')->get(),

                'users' =>
                    User::orderBy('name')->get(),

                'activityId' =>
                    $request->query('activity_id'),
            ]
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'activity_id' => [
                'required',
                'exists:activities,id',
            ],

            'assigned_to' => [
                'required',
                'exists:users,id',
            ],

            'priority' => [
                'required',
                'in:low,medium,high',
            ],

            'due_date' => [
                'nullable',
                'date',
            ],
        ]);

        Task::create([
            'title' =>
                $validated['title'],

            'description' =>
                $validated['description'] ?? null,

            'activity_id' =>
                $validated['activity_id'],

            'assigned_to' =>
                $validated['assigned_to'],

            'created_by' =>
                auth()->id(),

            'priority' =>
                $validated['priority'],

            'due_date' =>
                $validated['due_date'] ?? null,

            'status' =>
                'assigned',
        ]);

        return redirect()
            ->route('tasks.index')
            ->with(
                'success',
                'Task created successfully.'
            );
    }

    public function show(Task $task)
    {
        $task->load([
            'activity',
            'assignee',
            'creator',
        ]);

        return Inertia::render(
            'Tasks/Show',
            [
                'task' => $task,
            ]
        );
    }

    public function edit(Task $task)
    {
        return Inertia::render(
            'Tasks/Edit',
            [
                'task' => $task,

                'activities' =>
                    Activity::orderBy('name')->get(),

                'users' =>
                    User::orderBy('name')->get(),
            ]
        );
    }

    public function update(
        Request $request,
        Task $task
    ) {
        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'activity_id' => [
                'required',
                'exists:activities,id',
            ],

            'assigned_to' => [
                'required',
                'exists:users,id',
            ],

            'priority' => [
                'required',
                'in:low,medium,high',
            ],

            'due_date' => [
                'nullable',
                'date',
            ],
        ]);

        $task->update($validated);

        return redirect()
            ->route('tasks.show', $task)
            ->with(
                'success',
                'Task updated successfully.'
            );
    }

    public function destroy(Task $task)
    {
        $task->delete();

        return redirect()
            ->route('tasks.index')
            ->with(
                'success',
                'Task deleted successfully.'
            );
    }

    public function updateStatus(
        Request $request,
        Task $task
    ) {
        $validated = $request->validate([
            'status' => [
                'required',
                'in:assigned,accepted,in_progress,completed,cannot_do',
            ],
        ]);

        $task->status =
            $validated['status'];

        if (
            $validated['status'] === 'completed'
        ) {
            $task->completed_at = now();
        } else {
            $task->completed_at = null;
        }

        $task->save();

        return back()->with(
            'success',
            'Task status updated.'
        );
    }
}