<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $statistics = [
            'total' => Task::count(),

            'assigned' => Task::where(
                'status',
                'assigned'
            )->count(),

            'accepted' => Task::where(
                'status',
                'accepted'
            )->count(),

            'in_progress' => Task::where(
                'status',
                'in_progress'
            )->count(),

            'completed' => Task::where(
                'status',
                'completed'
            )->count(),

            'cannot_do' => Task::where(
                'status',
                'cannot_do'
            )->count(),

            'overdue' => Task::whereDate(
                'due_date',
                '<',
                today()
            )
                ->where('status', '!=', 'completed')
                ->count(),
        ];

        $recentTasks = Task::with([
            'activity',
            'assignee',
            'creator',
        ])
            ->latest()
            ->limit(10)
            ->get();

        $workerWorkloads = User::where('role', 'worker')
            ->with([
                'assignedTasks',
                'assignedTasks.activity',
                'assignedTasks.creator',
            ])
            ->get()
            ->map(function ($worker) {
                $tasks = $worker->assignedTasks;
                return [
                    'id' => $worker->id,
                    'name' => $worker->name,
                    'total' => $tasks->count(),
                    'assigned' => $tasks->where('status', 'assigned')->count(),
                    'accepted' => $tasks->where('status', 'accepted')->count(),
                    'in_progress' => $tasks->where('status', 'in_progress')->count(),
                    'completed' => $tasks->where('status', 'completed')->count(),
                    'cannot_do' => $tasks->where('status', 'cannot_do')->count(),
                    'overdue' => $tasks->filter(function ($task) {
                        if ($task->status === 'completed' || !$task->due_date) return false;
                        return \Carbon\Carbon::parse($task->due_date)->lt(today());
                    })->count(),
                    'tasks' => $tasks->load(['activity', 'creator']),
                ];
            });

        return Inertia::render('Dashboard', [
            'statistics' => $statistics,
            'recentTasks' => $recentTasks,
            'workerWorkloads' => $workerWorkloads,
        ]);
    }
}