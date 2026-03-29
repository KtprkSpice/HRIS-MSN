<?php

namespace App\Console\Commands;

use App\Models\Presence;
use App\Models\Schedule;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class autoAbsent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:auto-absent';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Log::info('Auto absent check started');

        $now = now();

        $schedules = Schedule::with('shift')
            ->whereDate('date', today())
            ->get();

        foreach ($schedules as $schedule) {

            $shiftStart = today()->setTimeFromTimeString($schedule->shift->start_time);

            // kasih toleransi 30 menit
            if ($now->lessThan($shiftStart->copy()->addMinutes(30))) {
                continue;
            }

            $presence = Presence::where('employee_id', $schedule->employee_id)
                ->where('schedule_id', $schedule->id)
                ->whereDate('date', today())
                ->first();

            // kalau belum presensi sama sekali
            if (! $presence) {

                Presence::create([
                    'employee_id' => $schedule->employee_id,
                    'task_id' => $schedule->task_id,
                    'schedule_id' => $schedule->id,
                    'shift_id' => $schedule->shift_id,
                    'date' => today(),
                    'check_in' => null,
                    'check_out' => null,
                    'status' => 'absent',
                    'late_minutes' => 0,
                ]);

                Log::info('Employee marked absent', [
                    'employee_id' => $schedule->employee_id,
                    'schedule_id' => $schedule->id,
                ]);
            }
        }

        Log::info('Auto absent finished');
    }
}
