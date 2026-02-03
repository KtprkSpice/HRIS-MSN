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
    public function handle(Task $task, Carbon $weekStart, bool $force = false): void
    {
        \Log::info('WEEKLY GENERATOR DIPANGGIL', [
            'task_id' => $task->id,
            'week' => $weekStart->toDateString(),
            'force' => $force,
        ]);

        $employees = $task->employees()
            ->wherePivotNull('deleted_at')
            ->get();

        if ($employees->isEmpty()) {
            throw new Exception('Task tidak memiliki employee');
        }

        // ================================
        // Ambil / Auto-generate shift rule
        // ================================
        $rules = TaskShiftRule::where('task_id', $task->id)->get();

        if ($rules->isEmpty()) {
            app(\App\Actions\GenerateTaskShiftRule::class)->handle($task);
            $rules = TaskShiftRule::where('task_id', $task->id)->get();
        }

        if ($rules->isEmpty()) {
            throw new Exception('TaskShiftRule gagal digenerate');
        }

        $totalMin = $rules->sum('min_employee');

        if ($employees->count() < $totalMin) {
            throw new Exception('Jumlah employee kurang dari kebutuhan minimum shift');
        }

        // ================================
        // REGENERATE MODE
        // ================================
        $exists = WeeklyShiftAssignment::where('task_id', $task->id)
            ->where('week_start_date', $weekStart->toDateString())
            ->exists();

        if ($exists && ! $force) {
            \Log::info('Weekly sudah ada, skip generate', [
                'task_id' => $task->id,
            ]);

            return;
        }

        DB::transaction(function () use ($task, $weekStart, $rules, $employees) {

            // 🔥 HAPUS WEEKLY LAMA
            WeeklyShiftAssignment::where('task_id', $task->id)
                ->where('week_start_date', $weekStart->toDateString())
                ->delete();

            // ================================
            // Last shift map
            // ================================
            $lastShiftMap = WeeklyShiftAssignment::where('task_id', $task->id)
                ->whereIn('employee_id', $employees->pluck('id'))
                ->orderByDesc('week_start_date')
                ->get()
                ->groupBy('employee_id')
                ->map(fn ($rows) => $rows->first()?->shift_id);

            // ================================
            // Slot shift
            // ================================
            $shiftSlots = [];
            foreach ($rules as $rule) {
                $shiftSlots[$rule->shift_id] = $rule->min_employee;
            }

            $employeePool = $employees->shuffle();
            $assignments = collect();

            // PASS 1
            foreach ($employeePool as $employee) {
                $lastShiftId = $lastShiftMap[$employee->id] ?? null;

                $possibleShifts = collect($shiftSlots)
                    ->filter(fn ($slot, $shiftId) => $slot > 0 && $shiftId != $lastShiftId);

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

            // PASS 2
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

            // INSERT
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
