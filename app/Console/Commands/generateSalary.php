<?php

namespace App\Console\Commands;

use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\Presence;
use App\Models\Salary;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class generateSalary extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-salary';

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

        Log::info('Salary generation started');

        $today = today();
        $start = $today->copy()->startOfMonth();
        $end = $today->copy()->day(28);

        // Duplication check
        if (Salary::whereDate('date', $start)->exists()) {
            Log::warning('Generate Salary Failed - already generated', [
                'period' => $start->format('Y-m'),
            ]);

            return;
        }

        $employees = Employee::whereHas('user.role', function ($q) {
            $q->whereIn('name', ['hr', 'employee']);
        })
            ->where('status', 'active')
            ->get();

        foreach ($employees as $employee) {
            try {

                // PRESENCE CUTS
                $presences = Presence::where('employee_id', $employee->id)
                    ->whereBetween('date', [$start, $end])
                    ->get();

                // Absence Cuts
                $absencesCuts = Presence::where('employee_id', $employee->id)
                    ->where('status', 'absent')
                    ->whereBetween('date', $start, $end)
                    ->get();

                $lateMinutes = $presences->sum('late_minutes');
                $lateCuts = $lateMinutes * 1000;

                //    LeaveCuts Default
                $leaveCuts = 0;

                $leaves = LeaveRequest::with('types')
                    ->where('employee_id', $employee->id)
                    ->where('status', 'confirmed')
                    ->where(function ($q) use ($start, $end) {
                        $q->whereBetween('start_date', [$start, $end])
                            ->orWhereBetween('end_date', [$start, $end]);
                    })
                    ->get();

                foreach ($leaves as $leave) {

                    $type = $leave->types;

                    // skip kalau paid atau data rusak
                    if (! $type || $type->is_paid) {
                        continue;
                    }

                    $leaveStart = Carbon::parse($leave->start_date)->max($start);
                    $leaveEnd = Carbon::parse($leave->end_date)->min($end);

                    $days = $leaveStart->diffInDays($leaveEnd) + 1;

                    $leaveCuts += $days * $type->deduction;
                }

                // Final Saalry
                $baseSalary = $employee->position->base_salary;
                $totalCuts = $lateCuts + $leaveCuts + $absencesCuts;
                $net = $baseSalary - $totalCuts;

                Salary::create([
                    'employee_id' => $employee->id,
                    'net_salary' => $baseSalary,
                    'cuts' => $totalCuts,
                    'bonus' => 0,
                    'date' => $start,
                    'total' => $net,
                ]);

                Log::info('Salary Generated', [
                    'employee_id' => $employee->id,
                    'period' => $start->format('Y-m'),
                    'late_cuts' => $lateCuts,
                    'leave_cuts' => $leaveCuts,
                    'total_cuts' => $totalCuts,
                    'total' => $net,
                ]);

            } catch (\Throwable $e) {
                Log::error('Generate Salary Error', [
                    'employee_id' => $employee->id,
                    'message' => $e->getMessage(),
                ]);
            }
        }

        Log::info('Salary generation ended');

        return Command::SUCCESS;
    }
}
