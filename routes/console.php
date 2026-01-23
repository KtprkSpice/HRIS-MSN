<?php

use App\Actions\GenerateWeeklyShiftAssignment;
use App\Models\QrCode;
use App\Models\Schedule as ScheduleModel;
use App\Models\Task;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Str;

// php artisan schedule:run
Schedule::everyMinute()->call(function () {

    \Log::info('QR SCHEDULER START');

    $workDate = today();

    // Ambil shift yang dipakai hari ini (tidak peduli employee)
    $rows = ScheduleModel::select('task_id', 'shift_id')
        ->whereBetween('date', [
            now()->startOfWeek(),
            now()->endOfWeek(),
        ])
        ->groupBy('task_id', 'shift_id')
        ->get();

    foreach ($rows as $row) {
        foreach (['check_in', 'check_out'] as $type) {

            $exists = QrCode::where('task_id', $row->task_id)
                ->where('shift_id', $row->shift_id)
                ->where('date', $workDate)
                ->where('type', $type)
                ->exists();

            if ($exists) {
                continue;
            }

            QrCode::create([
                'task_id' => $row->task_id,
                'shift_id' => $row->shift_id,
                'date' => $workDate, // 🔥 HARI KERJA
                'token' => Str::uuid(),
                'generated_at' => now(),
                'expires_at' => now()->addMinutes(30),
                'is_active' => true,
                'type' => $type,
            ]);

            \Log::info('QR TERBUAT', [
                'task_id' => $row->task_id,
                'shift_id' => $row->shift_id,
                'date' => $workDate,
                'type' => $type,
            ]);
        }
    }
});

Artisan::command('schedule:generate-weekly', function () {
    $this->info('Generate weekly shift assignment...');

    $weekStart = Carbon::now()->startOfWeek();

    Task::where('status', 'on duty')
        ->each(function ($task) use ($weekStart) {

            try {
                app(GenerateWeeklyShiftAssignment::class)
                    ->handle($task, $weekStart);

                $this->info("✓ Task {$task->name} selesai");

            } catch (\Throwable $e) {
                $this->error("✗ Task {$task->name}: {$e->getMessage()}");
            }
        });

    $this->info('SEMUA TASK SELESAI');
});

Schedule::command('schedule:generate-weekly')
    ->everyMinute();

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
