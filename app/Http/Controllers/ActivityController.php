<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ActivityController extends Controller
{
    public function index()
    {
        $activities = Activity::with(['tasks', 'creator'])
            ->latest()
            ->get();

        $activities->each(function ($activity) {
            $tasks = $activity->tasks;
            $activity->completed_tasks = $tasks->where('status', 'completed')->count();
            $activity->in_progress_tasks = $tasks->where('status', 'in_progress')->count();
            $activity->pending_tasks = $tasks->whereIn('status', ['assigned', 'accepted'])->count();
            $activity->cant_do_tasks = $tasks->where('status', 'cannot_do')->count();
        });

        return Inertia::render(
            'Activities/Index',
            [
                'activities' => $activities,
            ]
        );
    }

    public function create()
    {
        return Inertia::render(
            'Activities/Create'
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'icon' => [
                'nullable',
                'string',
                'max:10',
            ],
        ]);

        Activity::create([
            'name' => $validated['name'],

            'description' =>
                $validated['description'] ?? null,

            'icon' =>
                $validated['icon'] ?? null,

            'created_by' => auth()->id(),
        ]);

        return redirect()
            ->route('activities.index')
            ->with(
                'success',
                'Activity created successfully.'
            );
    }

    public function show(Activity $activity)
    {
        $activity->load([
            'creator',
            'tasks.assignee',
            'tasks.creator',
        ]);

        return Inertia::render(
            'Activities/Show',
            [
                'activity' => $activity,
            ]
        );
    }

    public function edit(Activity $activity)
    {
        return Inertia::render(
            'Activities/Edit',
            [
                'activity' => $activity,
            ]
        );
    }

    public function update(
        Request $request,
        Activity $activity
    ) {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'icon' => [
                'nullable',
                'string',
                'max:10',
            ],
        ]);

        $activity->update($validated);

        return redirect()
            ->route('activities.index')
            ->with(
                'success',
                'Activity updated successfully.'
            );
    }

    public function destroy(Activity $activity)
    {
        $activity->delete();

        return redirect()
            ->route('activities.index')
            ->with(
                'success',
                'Activity deleted successfully.'
            );
    }
}