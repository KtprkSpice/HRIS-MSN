<?php

namespace App\Http\Controllers;

use App\Models\Allowance;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\Presence;
use App\Models\Salary;
use App\Support\AttendancePolicy;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class SalaryController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $roles = auth()->user()->role->name;

        if ($roles === 'employee') {
            $salaries = Salary::where('employee_id', $user->employee->id)->get();
        } else {
            $salaries = Salary::all();
        }

        return view('Salary.index', compact('salaries'));
    }

    public function show(Salary $salary)
    {
        $period = Carbon::parse($salary->date);
        $start = $period->copy()->startOfMonth();
        $end = $period->copy()->day(28);

        $employee = Employee::with('position')->find($salary->employee_id);
        $presences = Presence::where('employee_id', $employee->id)
            ->whereBetween('date', [$start, $end])
            ->get();
        $absencesCuts = $salary->absent_cuts;
        $lateCuts = $salary->late_cuts;
        $allowances = Allowance::all();
        $baseSalary = $employee->position->base_salary;

        $cuts = [];

        foreach ($allowances as $item) {
            if ($item->calculation_type === 'fixed') {
                $value = $item->amount;
            } else {
                // percentage
                $value = ($item->percentage_value / 100) * $baseSalary;
            }

            $cuts[] = [
                'name' => $item->allowance_type,
                'value' => $value,
            ];
        }

        $allowanceCuts = collect($cuts)->sum('value');

        $tax = $salary->pph_cuts;
        $totalSalary = $salary->total;

        return view('Salary.show', compact('salary', 'absencesCuts', 'lateCuts', 'cuts', 'totalSalary', 'tax'));
    }

    public function create()
    {
        $roles = auth()->user()->role->name;

        if ($roles === 'employee') {
            abort(403);
        } else {
            $employees = Employee::with('position')->get();
        }

        return view('Salary.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $today = today();
        $start = $today->copy()->startOfMonth();
        $end = $today->copy()->day(28);

        $roles = auth()->user()->role->name;

        if ($roles === 'employee') {
            abort(403);
        } else {
            $request->validate([
                'employee_id' => 'required',
                'net_salary' => 'required',
                'bonus' => 'nullable',
                'date' => 'nullable',
                'bpjs_kesehatan_cuts' => 'required',
                'bpjs_ketenagakerjaan_cuts' => 'required',
                'absent_cuts' => 'nullable',
                'late_cuts' => 'nullable',
            ]);

            // Leave auto Cuts
            $leaveCuts = 0;

            $leaves = LeaveRequest::with('types')
                ->where('employee_id', $request->employee_id)
                ->whereIn('status', AttendancePolicy::APPROVED_LEAVE_STATUSES)
                ->where(function ($q) use ($start, $end) {
                    $q->whereBetween('start_date', [$start, $end])
                        ->orWhereBetween('end_date', [$start, $end]);
                })
                ->get();

            foreach ($leaves as $leave) {
                $type = $leave->types;

                if (! $type || $type->is_paid) {
                    continue;
                }

                $leaveStart = Carbon::parse($leave->start_date)->max($start);
                $leaveEnd = Carbon::parse($leave->end_date)->min($end);

                $days = $leaveStart->diffInDays($leaveEnd) + 1;
                $leaveCuts += $days * $type->deduction;
            }

            $net_salary = (int) str_replace('.', '', $request->net_salary);
            $bonus = (int) str_replace('.', '', $request->bonus);

            $bpjsKesehatanCuts = (int) str_replace('.', '', $request->bpjs_kesehatan_cuts);
            $bpjsKetenagakerjaanCuts = (int) str_replace('.', '', $request->bpjs_ketenagakerjaan_cuts);

            $absentCuts = (int) str_replace('.', '', $request->absent_cuts);
            $lateCuts = (int) str_replace('.', '', $request->late_cuts);

            $totalCuts = $bpjsKesehatanCuts +
                $bpjsKetenagakerjaanCuts +
                $absentCuts +
                $lateCuts +
                $leaveCuts;

            $beforeTax = round($net_salary + $bonus - $totalCuts);

            $tax = round(0.05 * $beforeTax);

            $total = $beforeTax - $tax;

            $request->merge([
                'net_salary' => $net_salary,
                'bonus' => $bonus,
                'bpjs_kesehatan_cuts' => $bpjsKesehatanCuts,
                'bpjs_ketenagakerjaan_cuts' => $bpjsKetenagakerjaanCuts,
                'absent_cuts' => $absentCuts,
                'late_cuts' => $lateCuts,
                'cuts' => $totalCuts + $tax,
                'pph_cuts' => $tax,
                'total' => $total,
            ]);

            Salary::create($request->all());
        }

        return redirect()->route('salary.index')->with('success', 'Data Berhasil ditambahkan');
    }

    public function edit(Salary $salary)
    {
        $roles = auth()->user()->role->name;

        if ($roles === 'employee') {
            abort(403);
        } else {
            $employees = Employee::all();
        }

        return view('Salary.edit', compact('salary', 'employees'));
    }

    public function update(Request $request, Salary $salary)
    {

        $roles = auth()->user()->role->name;

        if ($roles === 'employee') {
            abort(403);
        } else {
            $today = today();
            $start = $today->copy()->startOfMonth();
            $end = $today->copy()->day(28);

            $request->validate([
                'employee_id' => 'required',
                'net_salary' => 'required',
                'bonus' => 'nullable',
                'date' => 'nullable',
                'bpjs_kesehatan_cuts' => 'required',
                'bpjs_ketenagakerjaan_cuts' => 'required',
                'absent_cuts' => 'nullable',
                'late_cuts' => 'nullable',

            ]);

            $leaveCuts = 0;

            $leaves = LeaveRequest::with('types')
                ->where('employee_id', $request->employee_id)
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

            $net_salary = (int) str_replace('.', '', $request->net_salary);
            $bonus = (int) str_replace('.', '', $request->bonus);

            $bpjsKesehatanCuts = (int) str_replace('.', '', $request->bpjs_kesehatan_cuts);
            $bpjsKetenagakerjaanCuts = (int) str_replace('.', '', $request->bpjs_ketenagakerjaan_cuts);

            $absentCuts = (int) str_replace('.', '', $request->absent_cuts);
            $lateCuts = (int) str_replace('.', '', $request->late_cuts);

            $totalCuts = $bpjsKesehatanCuts + $bpjsKetenagakerjaanCuts + $absentCuts + $lateCuts + $leaveCuts;

            $beforeTax = round($net_salary + $bonus - $totalCuts);

            $tax = round(0.05 * $beforeTax);

            $total = $beforeTax - $tax;

            $request->merge([
                'net_salary' => $net_salary,
                'bonus' => $bonus,
                'bpjs_kesehatan_cuts' => $bpjsKesehatanCuts,
                'bpjs_ketenagakerjaan_cuts' => $bpjsKetenagakerjaanCuts,
                'absent_cuts' => $absentCuts,
                'late_cuts' => $lateCuts,
                'cuts' => $totalCuts + $tax,
                'pph_cuts' => $tax,
                'total' => $total,
            ]);

            $salary->update($request->all());
        }

        return redirect()->route('salary.index')->with('success', 'Data Telah diubah');
    }

    public function destroy(Salary $salary)
    {
        $roles = auth()->user()->role->name;

        if ($roles === 'employee') {
            abort(403);
        } else {
            $salary->delete();
        }

        return redirect()->route('salary.index')->with('success', 'Data Telah Dihapus');
    }

    // Generate Salary
    public function generate()
    {
        $roles = auth()->user()->role->name;

        if ($roles === 'employee') {
            abort(403);
        } else {
            // Start schedule
            Log::info('Auto Schedule Run');
            Artisan::call('app-auto-schedule');
            Log::info('Auto Schedule Stopped');

            // Start Absent
            Log::info('Auto Absent Run');
            Artisan::call('app:auto-absent');
            Log::info('Auto Absent Stopped');

            // Start Salry Generation
            Log::info('Salary generation started');

            $today = today();
            $start = $today->copy()->startOfMonth();
            $end = $today->copy()->day(28);

            // Duplication check
            if (Salary::whereDate('date', $start)->exists()) {
                Log::warning('Generate Salary Failed - already generated', [
                    'period' => $start->format('Y-m'),
                ]);

                return back()->with('error', 'Gaji periode ini sudah digenerate');
            }

            $employees = Employee::whereHas('user.role', function ($q) {
                $q->whereIn('name', ['hr', 'employee']);
            })
                ->where('status', 'active')
                ->get();

            // Allowance
            $bpjsKesehatan = Allowance::Where('allowance_type', 'BPJS Kesehatan')->first();
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
        }

        return back()->with('success', 'Gaji berhasil digenerate');
    }
}
