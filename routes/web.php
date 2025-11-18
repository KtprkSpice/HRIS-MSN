<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\PresecesController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SalaryController;
use App\Http\Controllers\TaskController;
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

    // Salary
    Route::resource('/salary', SalaryController::class);

    // Leave Request
    Route::resource('/leave-request',LeaveRequestController::class);

    // Presences
    Route::resource('/presence', PresecesController::class);
});
