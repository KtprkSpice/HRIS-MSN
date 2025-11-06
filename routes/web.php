<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Owner Route
Route::middleware(['auth', 'role:owner'])->group(function () {
    // Dashboard
    Route::resource('/dashboard', DashboardController::class);

    // Employees
    Route::resource('/employee', EmployeeController::class);

    // Task
    Route::resource('/task', TaskController::class);

    // Payroll
    Route::resource('/payroll', PayrollController::class);
});

// employee Route
Route::middleware(['auth', 'role:employee'])->group(function () {
    // Dashboard
    Route::resource('/dashboard', DashboardController::class);

    // Employees
    Route::resource('/employee', EmployeeController::class);

    // Payroll
    Route::resource('/payroll', PayrollController::class);
});
