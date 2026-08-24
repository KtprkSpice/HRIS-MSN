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
        // nyalain kalo production
        // if (! $today->isSameDay($today->copy()->day(29))) {
        //     Log::warning('Generate Salary Failed - invalid payroll date', [
        //         'date' => $today->toDateString(),
        //     ]);

        //     $this->error('Slip gaji hanya bisa digenerate pada tanggal 29.');

        //     return Command::FAILURE;
        // }

        $start = $today->copy()->startOfMonth();
        $payrollDate = $today->copy()->day(29);
        $attendanceCutoff = $payrollDate->copy()->subDay();

        $employees = Employee::whereHas('user.role', function ($q) {
            $q->whereIn('name', ['hr', 'employee']);
        })
            ->where('status', 'active')
            ->whereDate('hire_date', '<=', $payrollDate)
            ->get();

        // Allowance
        $bpjsKesehatan = Allowance::where('allowance_type', 'BPJS Kesehatan')->first();
        $bpjsKetenagakerjaan = Allowance::where('allowance_type', 'BPJS Ketenagakerjaan')->first();

        $generated = 0;
        $restored = 0;
        $skipped = 0;

        foreach ($employees as $employee) {

            try {
                $activeSalary = Salary::where('employee_id', $employee->id)
                    ->whereDate('date', $start)
                    ->first();

                if ($activeSalary) {
                    $skipped++;

                    Log::info('Salary skipped - already exists', [
                        'employee_id' => $employee->id,
                        'period' => $start->format('Y-m'),
                    ]);

                    continue;
                }

                $deletedSalary = Salary::onlyTrashed()
                    ->where('employee_id', $employee->id)
                    ->whereDate('date', $start)
                    ->latest('deleted_at')
                    ->first();

                $preseces = Presence::where('employee_id', $employee->id)
                    ->whereBetween('date', [$start, $attendanceCutoff])
                    ->whereHas('schedule')
                    ->get();

                $absentCuts = AttendancePolicy::payableAbsenceQuery(
                    $employee->id,
                    $start,
                    $attendanceCutoff,
                )->count() * 50000;

                $lateCount = $preseces->where('late_minutes', '>', 0)->count();
                $lateCuts = $lateCount * 15000;

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
                    ->where(function ($q) use ($start, $attendanceCutoff) {
                        $q->whereDate('start_date', '<=', $attendanceCutoff)
                            ->whereDate('end_date', '>=', $start);
                    })
                    ->get();

                foreach ($leaves as $leave) {

                    $type = $leave->types;

                    // skip kalau paid atau data rusak
                    if (! $type || $type->is_paid) {
                        continue;
                    }

                    $leaveStart = Carbon::parse($leave->start_date)->max($start);
                    $leaveEnd = Carbon::parse($leave->end_date)->min($attendanceCutoff);

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

                $salaryData = [
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
                ];

                if ($deletedSalary) {
                    unset($salaryData['created_at']);

                    $deletedSalary->restore();
                    $deletedSalary->update($salaryData);
                    $restored++;
                } else {
                    Salary::create($salaryData);
                    $generated++;
                }

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

        Log::info('Salary generation ended', [
            'period' => $start->format('Y-m'),
            'attendance_cutoff' => $attendanceCutoff->toDateString(),
            'generated' => $generated,
            'restored' => $restored,
            'skipped' => $skipped,
        ]);

        $this->info("Generate gaji periode {$start->format('Y-m')} selesai. Absensi dihitung sampai {$attendanceCutoff->toDateString()}. Baru: {$generated}, restore: {$restored}, dilewati: {$skipped}.");

        return Command::SUCCESS;
    }
}
