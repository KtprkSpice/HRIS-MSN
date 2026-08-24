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
            $salaries = Salary::where('employee_id', $user->employee->id)
                ->orderByDesc('date')
                ->get();
        } else {
            $salaries = Salary::orderByDesc('date')->get();
        }

        return view('Salary.index', compact('salaries'));
    }

    public function show(Salary $salary)
    {
        $period = Carbon::parse($salary->date);
        $start = $period->copy()->startOfMonth();
        // Tanggal 29 adalah hari generate/gajian, jadi absensi dihitung sampai tanggal 28.
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
            $employees = Employee::with('position')->where('status', 'active')->get();
        }

        return view('Salary.create', compact('employees'));
    }

    public function store(Request $request)
    {
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

            $period = $request->filled('date')
                ? Carbon::parse($request->date)
                : today();
            $start = $period->copy()->startOfMonth();
            // Tanggal 29 adalah hari generate/gajian, jadi absensi dihitung sampai tanggal 28.
            $end = $period->copy()->day(28);

            if (Salary::where('employee_id', $request->employee_id)->whereDate('date', $start)->exists()) {
                return back()
                    ->withInput()
                    ->withErrors(['date' => 'Slip gaji karyawan untuk periode ini sudah ada.']);
            }

            // Leave auto Cuts
            $leaveCuts = 0;

            $leaves = LeaveRequest::with('types')
                ->where('employee_id', $request->employee_id)
                ->whereIn('status', AttendancePolicy::APPROVED_LEAVE_STATUSES)
                ->where(function ($q) use ($start, $end) {
                    $q->whereDate('start_date', '<=', $end)
                        ->whereDate('end_date', '>=', $start);
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

            $absentCuts = AttendancePolicy::payableAbsenceQuery(
                $request->employee_id,
                $start,
                $end,
            )->count() * 50000;

            $lateCuts = Presence::where('employee_id', $request->employee_id)
                ->whereBetween('date', [$start, $end])
                ->whereHas('schedule')
                ->where('late_minutes', '>', 0)
                ->count() * 15000;

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
                'date' => $start->toDateString(),
            ]);

            Salary::create($request->only((new Salary())->getFillable()));
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

            $period = $request->filled('date')
                ? Carbon::parse($request->date)
                : Carbon::parse($salary->date);
            $start = $period->copy()->startOfMonth();
            // Tanggal 29 adalah hari generate/gajian, jadi absensi dihitung sampai tanggal 28.
            $end = $period->copy()->day(28);

            if (
                Salary::where('employee_id', $request->employee_id)
                    ->whereDate('date', $start)
                    ->where('id', '!=', $salary->id)
                    ->exists()
            ) {
                return back()
                    ->withInput()
                    ->withErrors(['date' => 'Slip gaji karyawan untuk periode ini sudah ada.']);
            }

            $leaveCuts = 0;

            $leaves = LeaveRequest::with('types')
                ->where('employee_id', $request->employee_id)
                ->whereIn('status', AttendancePolicy::APPROVED_LEAVE_STATUSES)
                ->where(function ($q) use ($start, $end) {
                    $q->whereDate('start_date', '<=', $end)
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
                $leaveEnd = Carbon::parse($leave->end_date)->min($end);

                $days = $leaveStart->diffInDays($leaveEnd) + 1;

                $leaveCuts += $days * $type->deduction;
            }

            $net_salary = (int) str_replace('.', '', $request->net_salary);
            $bonus = (int) str_replace('.', '', $request->bonus);

            $bpjsKesehatanCuts = (int) str_replace('.', '', $request->bpjs_kesehatan_cuts);
            $bpjsKetenagakerjaanCuts = (int) str_replace('.', '', $request->bpjs_ketenagakerjaan_cuts);

            $absentCuts = AttendancePolicy::payableAbsenceQuery(
                $request->employee_id,
                $start,
                $end,
            )->count() * 50000;

            $lateCuts = Presence::where('employee_id', $request->employee_id)
                ->whereBetween('date', [$start, $end])
                ->whereHas('schedule')
                ->where('late_minutes', '>', 0)
                ->count() * 15000;

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
                'date' => $start->toDateString(),
            ]);

            $salary->update($request->only($salary->getFillable()));
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
        $role = auth()->user()->role->name;

        if ($role === 'employee') {
            abort(403);
        }

        Log::info('Generate Salary Requested');

        $exitCode = Artisan::call('app:generate-salary');

        if ($exitCode !== 0) {
            return back()->with(
                'error',
                'Slip gaji hanya bisa digenerate pada tanggal 29.'
            );
        }

        return back()->with(
            'success',
            'Gaji berhasil digenerate'
        );
    }
}
