<?php

namespace App\Actions;

use App\Models\Schedule;
use App\Models\Shift;
use App\Models\Task;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class GenerateWeeklySchedule
{
    public function handle()
    {
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();

        Task::where('status', 'on duty')
            ->with('employees')
            ->each(function ($task) use ($weekStart, $weekEnd) {

                // Ambil employee
                $employees = $task->employees;

                if ($employees->isEmpty()) {
                    return;
                }

                // Cegah generate dobel
                $alreadyGenerated = Schedule::where('task_id', $task->id)
                    ->whereBetween('date', [$weekStart, $weekEnd])
                    ->exists();

                if ($alreadyGenerated) {
                    return;
                }

                $shifts = Shift::all();
                $shiftCount = $shifts->count();

                if ($shiftCount === 0) {
                    return;
                }

                // ================================
                // Ambil shift terakhir tiap employee
                // ================================
                $lastShiftMap = Schedule::where('task_id', $task->id)
                    ->whereIn('employee_id', $employees->pluck('id'))
                    ->latest('date')
                    ->get()
                    ->groupBy('employee_id')
                    ->map(fn ($rows) => $rows->first()?->shift_id);

                // ================================
                // Hitung jatah per shift
                // ================================
                $totalEmployees = $employees->count();
                $baseQuota = intdiv($totalEmployees, $shiftCount);
                $remainder = $totalEmployees % $shiftCount;

                $shiftSlots = [];

                foreach ($shifts as $shift) {
                    $shiftSlots[$shift->id] = $baseQuota + ($remainder-- > 0 ? 1 : 0);
                }

                // ================================
                // Acak employee
                // ================================
                $employeePool = $employees->shuffle();

                $assignments = collect();

                // ================================
                // PASS 1 — Hindari shift sebelumnya
                // ================================
                foreach ($employeePool as $employee) {
                    $lastShiftId = $lastShiftMap[$employee->id] ?? null;

                    $possibleShifts = $shifts->filter(function ($shift) use ($lastShiftId, $shiftSlots) {
                        return $shiftSlots[$shift->id] > 0
                            && $shift->id !== $lastShiftId;
                    });

                    if ($possibleShifts->isEmpty()) {
                        continue;
                    }

                    $shift = $possibleShifts->random();

                    $assignments->push([
                        'employee_id' => $employee->id,
                        'shift_id' => $shift->id,
                    ]);

                    $shiftSlots[$shift->id]--;
                    $employeePool = $employeePool->reject(fn ($e) => $e->id === $employee->id);
                }

                // ================================
                // PASS 2 — Isi sisa slot (fallback)
                // ================================
                foreach ($employeePool as $employee) {
                    $possibleShifts = $shifts->filter(fn ($shift) => $shiftSlots[$shift->id] > 0);

                    if ($possibleShifts->isEmpty()) {
                        break;
                    }

                    $shift = $possibleShifts->random();

                    $assignments->push([
                        'employee_id' => $employee->id,
                        'shift_id' => $shift->id,
                    ]);

                    $shiftSlots[$shift->id]--;
                }

                // ================================
                // INSERT (TRANSACTION)
                // ================================
                DB::transaction(function () use ($assignments, $task, $weekStart) {
                    foreach ($assignments as $row) {
                        Schedule::create([
                            'employee_id' => $row['employee_id'],
                            'task_id' => $task->id,
                            'shift_id' => $row['shift_id'],
                            'date' => $weekStart,
                            'source' => 'system',
                        ]);
                    }
                });
            });
    }
}
