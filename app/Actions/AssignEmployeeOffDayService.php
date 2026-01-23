<?php

namespace App\Actions;

use App\Models\Employee;
use App\Models\EmployeeOffDay;
use App\Models\Task;
use App\Models\TaskShiftRule;

class AssignEmployeeOffDayService
{
    public function assign(Employee $employee, Task $task)
    {
        // Cegah dobel
        if (
            EmployeeOffDay::where('employee_id', $employee->id)
                ->where('task_id', $task->id)
                ->exists()
        ) {
            return;
        }

        $totalEmployees = $task->employees()->count();

        $minPerShift = TaskShiftRule::where('task_id', $task->id)
            ->sum('min_employee');

        $maxOffPerDay = $totalEmployees - $minPerShift;

        if ($maxOffPerDay <= 0) {
            throw new \Exception('Tidak ada slot libur tersedia');
        }

        // Hitung off per hari
        $offCount = EmployeeOffDay::where('task_id', $task->id)
            ->selectRaw('day_of_week, COUNT(*) as total')
            ->groupBy('day_of_week')
            ->pluck('total', 'day_of_week');

        // Cari hari paling sepi
        for ($day = 1; $day <= 7; $day++) {
            $current = $offCount[$day] ?? 0;

            if ($current < $maxOffPerDay) {
                EmployeeOffDay::create([
                    'employee_id' => $employee->id,
                    'task_id' => $task->id,
                    'day_of_week' => $day,
                ]);

                return;
            }
        }

        throw new \Exception('Semua hari sudah penuh libur');
    }
}
