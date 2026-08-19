<?php

namespace App\Console\Commands;

use App\Models\Employee;
use App\Models\EmployeeOffDay;
use App\Models\Schedule;
use App\Models\Task;
use App\Models\TaskShiftRule;
use App\Support\AttendancePolicy;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class autoSchedule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:auto-schedule';

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
        DB::transaction(function () {

            $weekStart = Carbon::now()->startOfWeek();
            $weekEnd = Carbon::now()->endOfWeek();

            // 1. Ambil task aktif
            $tasks = Task::where('status', 'on duty')->get();

            if ($tasks->isEmpty()) {
                throw new \Exception('Tidak ada task aktif.');
            }

            foreach ($tasks as $task) {

                // 2. Ambil employee task
                $employees = Employee::whereHas('tasks', function ($q) use ($task) {
                    $q->where('task_id', $task->id);
                })->where('status', 'active')->get();

                if ($employees->isEmpty()) {
                    continue;
                }

                // 3. Ambil / buat shift rule
                $rules = TaskShiftRule::where('task_id', $task->id)
                    ->get()
                    ->keyBy('shift_id');

                if ($rules->isEmpty()) {
                    foreach ([1, 2, 3] as $shiftId) {
                        TaskShiftRule::create([
                            'task_id' => $task->id,
                            'shift_id' => $shiftId,
                            'min_employee' => 1,
                        ]);
                    }

                    $rules = TaskShiftRule::where('task_id', $task->id)
                        ->get()
                        ->keyBy('shift_id');
                }

                $shiftCount = $rules->count();

                // 4. Pastikan 1 hari libur / minggu
                foreach ($employees as $employee) {
                    EmployeeOffDay::firstOrCreate(
                        [
                            'employee_id' => $employee->id,
                            'task_id' => $task->id,
                        ],
                        [
                            'day_of_week' => rand(0, 6),
                        ]
                    );
                }

                // 5. Loop per hari
                for ($date = $weekStart->copy(); $date <= $weekEnd; $date->addDay()) {

                    $dayOfWeek = $date->dayOfWeek;

                    $offEmployeeIds = EmployeeOffDay::where('task_id', $task->id)
                        ->where('day_of_week', $dayOfWeek)
                        ->pluck('employee_id');

                    $leaveEmployeeIds = $employees
                        ->filter(fn ($employee) => AttendancePolicy::hasApprovedLeaveOnDate($employee->id, $date))
                        ->pluck('id');

                    // Jadwal yang sudah ada tidak dibuat ulang. Hanya slot yang belum ada
                    // untuk task dan tanggal ini yang akan diproses.
                    $scheduledEmployeeIds = Schedule::where('task_id', $task->id)
                        ->whereDate('date', $date)
                        ->pluck('employee_id');

                    $availableEmployees = $employees
                        ->whereNotIn('id', $offEmployeeIds)
                        ->whereNotIn('id', $leaveEmployeeIds)
                        ->whereNotIn('id', $scheduledEmployeeIds)
                        ->shuffle()
                        ->values();

                    if ($availableEmployees->isEmpty()) {
                        continue;
                    }

                    $usedEmployeeIds = collect();

                    // 6. Hitung distribusi ideal
                    $idealPerShift = max(
                        1,
                        floor($availableEmployees->count() / $shiftCount)
                    );

                    // 7. Generate per shift (FAIR)
                    foreach ($rules as $shiftId => $rule) {

                        $needed = max($rule->min_employee, $idealPerShift);

                        $candidates = $availableEmployees
                            ->whereNotIn('id', $usedEmployeeIds)
                            ->take($needed);

                        foreach ($candidates as $employee) {
                            Schedule::firstOrCreate(
                                [
                                    'employee_id' => $employee->id,
                                    'task_id' => $task->id,
                                    'date' => $date->toDateString(),
                                ],
                                [
                                    'shift_id' => $shiftId,
                                    'source' => 'system',
                                ]
                            );

                            $usedEmployeeIds->push($employee->id);
                        }
                    }
                }
            }
        });

        $this->info('schedule tergenerate automatis');

        return Command::SUCCESS;
    }
}
