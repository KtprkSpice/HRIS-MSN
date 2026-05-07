<?php

namespace App\Console\Commands;

use App\Models\Allowance;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\Presence;
use App\Models\Salary;
use App\Support\AttendancePolicy;
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
                $preseces = Presence::where('employee_id', $employee->id)
                    ->whereBetween('date', [$start, $end])
                    ->get();

                $absentCuts = AttendancePolicy::payableAbsenceQuery(
                    $employee->id,
                    $start,
                    $end,
                )->count() * 50000;

                $lateMinutes = $preseces->sum('late_minutes');
                $cutPerMinutes = $employee->position->cut_per_minute ?? 0;
                $lateCuts = $lateMinutes * $cutPerMinutes;

                $baseSalary = $employee->position->base_salary;

                // Default Bonuis value
                $bonus = 0;

                // default BPJS VBalue

                $bpjsKesehatanCuts = 0;

                if ($bpjsKesehatan) {

                    if ($bpjsKesehatan->calculation_type === 'fixed') {

                        $bpjsKesehatanCuts =
                            $bpjsKesehatan->amount;

                    } else {

                        $bpjsKesehatanCuts =
                            ($bpjsKesehatan->percentage_value / 100)
                            * $baseSalary;
                    }
                }

                // BPJS KETENAGAKERJAAN DEFAULT CUts
                $bpjsKetenagakerjaanCuts = 0;

                if ($bpjsKetenagakerjaan) {
                    if ($bpjsKetenagakerjaan->calculation_type === 'fixed') {
                        $bpjsKetenagakerjaanCuts = $bpjsKetenagakerjaan->amount;
                    } else {
                        $bpjsKetenagakerjaanCuts = ($bpjsKetenagakerjaan->percentage_value / 100) * $baseSalary;
                    }
                }

                //    LeaveCuts Default
                $leaveCuts = 0;

                $leaves = LeaveRequest::with('types')
                    ->where('employee_id', $employee->id)
                    ->whereIn('status', AttendancePolicy::APPROVED_LEAVE_STATUSES)
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

                // TOTAL CUTS
                $totalCuts =
                $bpjsKesehatanCuts +
                $bpjsKetenagakerjaanCuts +
                $absentCuts +
                $lateCuts +
                $leaveCuts;

                // BEFORE TAXs
                $beforeTax = round(
                    $baseSalary +
                    $bonus -
                    $totalCuts
                );

                // PAJAK PPH
                $tax = round(0.05 * $beforeTax);

                // Total Salary
                $total = $beforeTax - $tax;

                Salary::create([
                    'employee_id' => $employee->id,
                    'net_salary' => $baseSalary,
                    'cuts' => $totalCuts + $tax,
                    'bonus' => $bonus,
                    'pph_cuts' => $tax,
                    'late_cuts' => $lateCuts,
                    'absent_cuts' => $absentCuts,
                    'bpjs_ketenagakerjaan_cuts' => $bpjsKetenagakerjaanCuts,
                    'bpjs_kesehatan_cuts' => $bpjsKesehatanCuts,
                    'leave_cuts' => $leaveCuts,
                    'total' => $total,
                    'date' => $start,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]);

                Log::info('Salary Generated', [
                    'employee_id' => $employee->id,

                    'period' => $start->format('Y-m'),

                    'bpjs_kesehatan' => $bpjsKesehatanCuts,

                    'bpjs_ketenagakerjaan' => $bpjsKetenagakerjaanCuts,

                    'late_cuts' => $lateCuts,

                    'absent_cuts' => $absentCuts,

                    'leave_cuts' => $leaveCuts,

                    'pph_21' => $tax,

                    'total' => $total,
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
