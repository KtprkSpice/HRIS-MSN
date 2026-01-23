<?php

namespace App\Actions;

use App\Models\Shift;
use App\Models\Task;
use App\Models\TaskShiftRule;
use Exception;

class GenerateTaskShiftRule
{
    public function handle(Task $task): void
    {
        // Ambil employee aktif di task
        $employeeCount = $task->employees()
            ->wherePivotNull('deleted_at')
            ->count();

        if ($employeeCount === 0) {
            throw new Exception('Tidak ada employee aktif untuk task ini');
        }

        // Ambil semua shift
        $shifts = Shift::all();
        $shiftCount = $shifts->count();

        if ($shiftCount === 0) {
            throw new Exception('Shift belum diset');
        }

        // Bagi rata
        $base = intdiv($employeeCount, $shiftCount);
        $remainder = $employeeCount % $shiftCount;

        foreach ($shifts as $shift) {
            TaskShiftRule::updateOrCreate(
                [
                    'task_id' => $task->id,
                    'shift_id' => $shift->id,
                ],
                [
                    'min_employee' => $base + ($remainder-- > 0 ? 1 : 0),
                ]
            );
        }
    }
}
