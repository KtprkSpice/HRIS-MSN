<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\LeaveTypeController;
use App\Http\Controllers\PresecesController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QrController;
use App\Http\Controllers\SalaryController;
use App\Http\Controllers\SchedulesController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Owner Route
Route::middleware(['auth', 'roles:owner,hr,employee'])->group(function () {
    // Dashboard
    Route::resource('/dashboard', DashboardController::class);

    // Edit Profile
    Route::get('/profile/{id}/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile/{id}/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('profile/password', function () {
        return view('Profile.password');
    })->name('profile.password');

    // Employees
    Route::resource('/employee', EmployeeController::class);

    // Task
    Route::resource('/task', TaskController::class);
    Route::get('/task/{id}/done', [TaskController::class, 'done'])->name('task.done');
    Route::get('/task/{id}/pending', [TaskController::class, 'pending'])->name('task.pending');
    Route::get('/task/{id}/onduty', [TaskController::class, 'onduty'])->name('task.onduty');

    // Salary
    Route::get('/salary/generate', [SalaryController::class, 'generate'])->name('salary.generate');
    Route::resource('/salary', SalaryController::class);

    // Qr
    Route::get('qr/generate', [QrController::class, 'generate'])->name('qr.generate');
    Route::get('/qr/{task}', [QrController::class, 'show'])->name('qr.show');

    // Leave Request
    Route::resource('/leave-request', LeaveRequestController::class);
    Route::get('/leave-request/{id}/pending', [LeaveRequestController::class, 'pending'])->name('leave-request.pending');
    Route::get('/leave-request/{id}/confirmed', [LeaveRequestController::class, 'confirmed'])->name('leave-request.confirmed');
    Route::get('/leave-request/{id}/rejected', [LeaveRequestController::class, 'rejected'])->name('leave-request.rejected');
    Route::post('/leave-request/{id}/approve', [LeaveRequestController::class, 'approve'])->name('leave-request.approve');

    // Leave Type
    Route::resource('/leave-type', LeaveTypeController::class);

    // Presences
    Route::resource('/presence', PresecesController::class)->except(['show']);
    Route::get('/presence/{task}', [PresecesController::class, 'scan'])->name('presences.scan');
    Route::post('/presence/qr/store', [PresecesController::class, 'storeQr'])->name('presences.storeQr');

    // Division
    Route::resource('/division', DivisionController::class);

    // Schedules
    Route::get('/schedule/generate', [SchedulesController::class, 'generate'])->name('schedule.generate');
    Route::resource('/schedule', SchedulesController::class);
});
