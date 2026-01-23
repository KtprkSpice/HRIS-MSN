<?php

namespace App\Actions;

use App\Models\EmployeeOffDay;
use App\Models\Schedule as ScheduleModel;
use App\Models\Task;
use App\Models\WeeklyShiftAssignment;
use Carbon\Carbon;

class GenerateDailySchedule
{
    public function handle(Task $task, Carbon $weekStart): void
    {

        \Log::info('DAILY GENERATOR DIPANGGIL', [
            'task_id' => $task->id,
            'week' => $weekStart->toDateString(),
        ]);

        $weeklyAssignments = WeeklyShiftAssignment::where('task_id', $task->id)
            ->where('week_start_date', $weekStart->toDateString())
            ->get();

        if ($weeklyAssignments->isEmpty()) {
            return;
        }

        // Loop 1 minggu
        for ($i = 0; $i < 7; $i++) {
            $date = $weekStart->copy()->addDays($i);

            foreach ($weeklyAssignments as $assign) {

                // Cek off day
                $offDay = EmployeeOffDay::where('employee_id', $assign->employee_id)
                    ->where('task_id', $task->id)
                    ->value('day_of_week');

                if ($offDay !== null && $offDay == $date->dayOfWeek) {
                    continue;
                }

                ScheduleModel::updateOrCreate(
                    [
                        'employee_id' => $assign->employee_id,
                        'task_id' => $task->id,
                        'date' => $date->toDateString(),
                    ],
                    [
                        'shift_id' => $assign->shift_id,
                        'source' => 'system',
                    ]
                );
            }
        }
    }
}
