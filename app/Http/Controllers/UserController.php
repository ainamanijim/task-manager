<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('name')->get();

        return Inertia::render(
            'Users/Index',
            [
                'users' => $users,
            ]
        );
    }

    public function show(User $user)
    {
        $user->load([
            'assignedTasks.activity',
        ]);

        return Inertia::render(
            'Users/Show',
            [
                'user' => $user,
            ]
        );
    }
}
