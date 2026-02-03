<?php

namespace App\Actions;

use App\Models\EmployeeOffDay;
use App\Models\Schedule;
use App\Models\Task;
use Carbon\Carbon;

class GenerateDailySchedule
{
    public function handle(Task $task, Carbon $weekStart): void
    {
        $employees = $task->employees()
            ->wherePivotNull('deleted_at')
            ->pluck('employees.id')
            ->toArray();

        if (empty($employees)) {
            return;
        }

        $shiftRules = $task->shiftRules()->with('shift')->get();

        if ($shiftRules->isEmpty()) {
            return;
        }

        // Loop 1 minggu
        for ($i = 0; $i < 7; $i++) {
            $date = $weekStart->copy()->addDays($i);

            if (
                $date->lt(Carbon::parse($task->start_time)) ||
                $date->gt(Carbon::parse($task->end_time))
            ) {
                continue;
            }

            $availableEmployees = collect($employees)->shuffle();

            foreach ($shiftRules as $rule) {
                $assigned = 0;

                foreach ($availableEmployees as $key => $employeeId) {

                    // Cek off day
                    $offDays = EmployeeOffDay::where('employee_id', $employeeId)
                        ->where('task_id', $task->id)
                        ->pluck('day_of_week')
                        ->toArray();

                    if (in_array($date->dayOfWeek, $offDays)) {
                        continue;
                    }

                    Schedule::updateOrCreate(
                        [
                            'employee_id' => $employeeId,
                            'task_id' => $task->id,
                            'date' => $date->toDateString(),
                        ],
                        [
                            'shift_id' => $rule->shift_id,
                            'source' => 'system',
                        ]
                    );

                    $availableEmployees->forget($key);
                    $assigned++;

                    if ($assigned >= $rule->min_employee) {
                        break;
                    }
                }
            }
        }
    }
}
