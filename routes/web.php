<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Dashboard
Route::resource('/dashboard', DashboardController::class);

// Employees
Route::resource('/employee', EmployeeController::class);

// Task
Route::resource('/task', TaskController::class);

// Payroll
Route::resource('/payroll', PayrollController::class);
