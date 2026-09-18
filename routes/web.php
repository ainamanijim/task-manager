<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth'])->group(function () {

    /*
      Dashboard
    */

    Route::get(
        '/dashboard',
        [DashboardController::class, 'index']
    )->name('dashboard');


    /*
      Activities
    */

    Route::resource(
        'activities',
        ActivityController::class
    );


    /*
    Tasks
    */

    Route::resource(
        'tasks',
        TaskController::class
    );

    Route::patch(
        '/tasks/{task}/status',
        [TaskController::class, 'updateStatus']
    )->name('tasks.status');


    /*
    Users
    */

    Route::resource(
        'users',
        UserController::class
    )->only([
        'index',
        'show',
    ]);
});

require __DIR__.'/auth.php';