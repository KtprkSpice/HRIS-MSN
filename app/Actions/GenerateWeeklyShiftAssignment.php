<?php

namespace App\Actions;

use App\Models\Shift;
use App\Models\Task;
use App\Models\TaskShiftRule;
use App\Models\WeeklyShiftAssignment;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class GenerateWeeklyShiftAssignment
{
    public function handle(Task $task, Carbon $weekStart): void
    {

        \Log::info('WEEKLY GENERATOR DIPANGGIL', [
            'task_id' => $task->id,
            'week' => $weekStart->toDateString(),
        ]);
        $employees = $task->employees()->wherePivotNull('deleted_at')->get();

        if ($employees->isEmpty()) {
            throw new Exception('Task tidak memiliki employee');
        }

        // ================================
        // Ambil rule shift
        // ================================
        $rules = TaskShiftRule::where('task_id', $task->id)
            ->with('shift')
            ->get();

        if ($rules->isEmpty()) {
            throw new Exception('TaskShiftRule belum diset');
        }

        $totalMin = $rules->sum('min_employee');

        if ($employees->count() < $totalMin) {
            throw new Exception('Jumlah employee kurang dari kebutuhan minimum shift');
        }

        // ================================
        // Cegah generate dobel
        // ================================
        $exists = WeeklyShiftAssignment::where('task_id', $task->id)
            ->where('week_start_date', $weekStart->toDateString())
            ->exists();

        if ($exists) {
            throw new Exception('Weekly shift sudah digenerate');
        }

        // ================================
        // Ambil shift terakhir employee
        // ================================
        $lastShiftMap = WeeklyShiftAssignment::where('task_id', $task->id)
            ->whereIn('employee_id', $employees->pluck('id'))
            ->orderByDesc('week_start_date')
            ->get()
            ->groupBy('employee_id')
            ->map(fn ($rows) => $rows->first()?->shift_id);

        // ================================
        // Slot shift (berdasarkan rule)
        // ================================
        $shiftSlots = [];

        foreach ($rules as $rule) {
            $shiftSlots[$rule->shift_id] = $rule->min_employee;
        }

        // ================================
        // Pool employee
        // ================================
        $employeePool = $employees->shuffle();
        $assignments = collect();

        // ================================
        // PASS 1 — Hindari shift minggu lalu
        // ================================
        foreach ($employeePool as $employee) {
            $lastShiftId = $lastShiftMap[$employee->id] ?? null;

            $possibleShifts = collect($shiftSlots)
                ->filter(fn ($slot, $shiftId) => $slot > 0 && $shiftId != $lastShiftId
                );

            if ($possibleShifts->isEmpty()) {
                continue;
            }

            $shiftId = $possibleShifts->keys()->random();

            $assignments->push([
                'employee_id' => $employee->id,
                'shift_id' => $shiftId,
            ]);

            $shiftSlots[$shiftId]--;
            $employeePool = $employeePool->reject(fn ($e) => $e->id === $employee->id);
        }

        // ================================
        // PASS 2 — Fallback isi sisa slot
        // ================================
        foreach ($employeePool as $employee) {
            $possibleShifts = collect($shiftSlots)->filter(fn ($slot) => $slot > 0);

            if ($possibleShifts->isEmpty()) {
                break;
            }

            $shiftId = $possibleShifts->keys()->random();

            $assignments->push([
                'employee_id' => $employee->id,
                'shift_id' => $shiftId,
            ]);

            $shiftSlots[$shiftId]--;
        }

        // ================================
        // INSERT (TRANSACTION)
        // ================================
        DB::transaction(function () use ($assignments, $task, $weekStart) {
            foreach ($assignments as $row) {
                WeeklyShiftAssignment::create([
                    'employee_id' => $row['employee_id'],
                    'task_id' => $task->id,
                    'shift_id' => $row['shift_id'],
                    'week_start_date' => $weekStart->toDateString(),
                ]);
            }
        });
    }
}
