<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Owner Route
Route::middleware('auth')->group(function () {
    
});

// Dashboard
    Route::resource('/dashboard', DashboardController::class)->middleware(['roles:owner,admin,employee']);

    //Edit Profile
    Route::get('/profile/{id}/edit', [ProfileController::class, 'edit'])->name('profile.edit')->middleware(['roles:owner,admin,employee']);

    // Employees
    Route::resource('/employee', EmployeeController::class)->middleware(['roles:owner,admin,employee']);

    // Task
    Route::resource('/task', TaskController::class)->middleware(['roles:owner,admin,employee']);

    // Payroll
    Route::resource('/payroll', PayrollController::class)->middleware(['roles:owner,admin,employee']);