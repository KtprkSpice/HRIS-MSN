<?php

namespace App\Console\Commands;

use App\Models\Allowance;
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

        // Allowance
        $bpjsKesehatan = Allowance::where('allowance_type', 'BPJS Kesehatan')->first();
        $bpjsKetenagakerjaan = Allowance::where('allowance_type', 'BPJS Ketenagakerjaan')->first();

        foreach ($employees as $employee) {
            try {

                // PRESENCE CUTS
                $presences = Presence::where('employee_id', $employee->id)
                    ->whereBetween('date', [$start, $end])
                    ->get();

                // Absence Cuts
                $absencesCuts = Presence::where('employee_id', $employee->id)
                    ->where('status', 'absent')
                    ->whereBetween('date', [$start, $end])
                    ->count() * 50000;

                // Allowance Cuts

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
                $allowanceCuts = 0;

                // Bpjs Kesehatan
                if (! is_null($employee->bpjs_kesehatan) && $bpjsKesehatan) {
                    if ($bpjsKesehatan->calculation_type === 'fixed') {
                        $bpjsValue = $allowanceCuts += $bpjsKesehatan->amount;
                    }

                    if ($bpjsKesehatan->calculation_type === 'percentage') {
                        $bpjsValue = $allowanceCuts += ($baseSalary * $bpjsKesehatan->percentage_value) / 100;
                    }
                }

                // Bpjs Ketengakerjaan
                if (! is_null($employee->bpjs_ketenagakerjaan) && $bpjsKetenagakerjaan) {
                    if ($bpjsKetenagakerjaan->calculation_type === 'fixed') {
                        $bpjsValue = $allowanceCuts += $bpjsKetenagakerjaan->amount;
                    }

                    if ($bpjsKetenagakerjaan->calculation_type === 'percentage') {
                        $bpjsValue = $allowanceCuts += ($baseSalary * $bpjsKetenagakerjaan->percentage_value) / 100;
                    }
                }

                $allowanceCuts = round($bpjsValue);
                $totalCuts = $lateCuts + $leaveCuts + $absencesCuts + $allowanceCuts;
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
                    'allowance_cuts' => $allowanceCuts,
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
