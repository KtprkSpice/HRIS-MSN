<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\PresecesController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SalaryController;
use App\Http\Controllers\TaskController;
use App\Models\Task;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Owner Route
Route::middleware(['auth', 'roles:owner,employee,hr'])->group(function () {
    // Dashboard
    Route::resource('/dashboard', DashboardController::class);

    //Edit Profile
    Route::get('/profile/{id}/edit', [ProfileController::class, 'edit'])
        ->name('profile.edit')
        ;

    // Employees
    Route::resource('/employee', EmployeeController::class);

    // Task
    Route::resource('/task', TaskController::class);
    Route::get('/task/{id}/done', [TaskController::class, 'done'])->name('task.done');
    Route::get('/task/{id}/pending', [TaskController::class, 'pending'])->name('task.pending');
    Route::get('/task/{id}/onduty', [TaskController::class, 'onduty'])->name('task.onduty');

    // Salary
    Route::resource('/salary', SalaryController::class);

    // Leave Request
    Route::resource('/leave-request',LeaveRequestController::class);

    // Presences
    Route::resource('/presence', PresecesController::class)->except(['show']);
    Route::get('/presence/{id}' ,[PresecesController::class, 'scan'])->name('presences.scan');

});
