<?php

namespace App\Console\Commands;

use App\Models\Presence;
use App\Models\Schedule;
use App\Support\AttendancePolicy;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class autoAbsent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:auto-absent {date?}';

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
        $workDate = $this->argument('date')
            ? Carbon::parse($this->argument('date'))
            : today();

        Log::info('Auto absent check started', [
            'date' => $workDate->toDateString(),
        ]);

        $now = now();

        $schedules = Schedule::with('shift')
            ->whereDate('date', $workDate)
            ->get();

        foreach ($schedules as $schedule) {

            if (
                AttendancePolicy::hasApprovedLeaveOnDate(
                    $schedule->employee_id,
                    $schedule->date
                )
            ) {
                continue;
            }

            $shiftStart = Carbon::parse($workDate->toDateString())
                ->setTimeFromTimeString(
                    $schedule->shift->start_time
                );

            if (
                $workDate->isToday() &&
                $now->lessThan(
                    $shiftStart->copy()->addMinutes(30)
                )
            ) {
                continue;
            }

            $presence = Presence::where(
                'employee_id',
                $schedule->employee_id
            )
                ->where('schedule_id', $schedule->id)
                ->whereDate('date', $workDate)
                ->first();

            if (! $presence) {

                Presence::create([
                    'employee_id' => $schedule->employee_id,
                    'task_id' => $schedule->task_id,
                    'schedule_id' => $schedule->id,
                    'shift_id' => $schedule->shift_id,
                    'date' => $workDate,
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
