<?php

use App\Models\QrCode;
use App\Models\Task;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Str;

// php artisan schedule:run
Schedule::everyMinute()->call(function () {
    $tasks = Task::where('status', 'on duty')->get();

    foreach ($tasks as $task) {
        if (! QrCode::where('task_id', $task->id)->where('type', 'check_in')->whereDate('date', today())->exists()) {
            QrCode::create([
                'task_id' => $task->id,
                'token' => Str::uuid(),
                'date' => today(),
                'generated_at' => now(),
                'expires_at' => now()->addMinutes(50),
                'is_active' => true,
                'type' => 'check_in',
            ]);
        }

        if (! QrCode::where('task_id', $task->id)->where('type', 'check_out')->whereDate('date', today())->exists()) {
            QrCode::create([
                'task_id' => $task->id,
                'token' => Str::uuid(),
                'date' => today(),
                'generated_at' => today(),
                'expires_at' => now()->addMinutes(50),
                'is_active' => true,
                'type' => 'check_out',
            ]);
        }
    }
});

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
