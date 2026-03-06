<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\Presence;
use App\Models\Salary;
use Carbon\Carbon;
use Illuminate\Http\Request;
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

    public function create()
    {
        $roles = auth()->user()->role->name;

        if ($roles === 'employee') {
            abort(403);
        } else {
            $employees = Employee::all();
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
                'cuts' => 'nullable',
                'bonus' => 'nullable',
                'date' => 'nullable',
            ]);

            $salary = (int) str_replace('.', '', $request->net_salary);
            $bonus = (int) str_replace('.', '', $request->bonus);
            $cuts = (int) str_replace('.', '', $request->cuts);

            $request->merge([
                'net_salary' => $salary,
                'bonus' => $bonus,
                'cuts' => $cuts,
                'total' => $salary + $bonus - $cuts,
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
            $request->validate([
                'employee_id' => 'required',
                'net_salary' => 'required',
                'cuts' => 'nullable',
                'bonus' => 'nullable',
                'date' => 'nullable',
            ]);

            $net = (int) str_replace('.', '', $request->net_salary);
            $cuts = (int) str_replace('.', '', $request->cuts);
            $bonus = (int) str_replace('.', '', $request->bonus);

            $request->merge([
                'net_salary' => $net,
                'bonus' => $bonus,
                'cuts' => $cuts,
                'total' => $net + $bonus - $cuts,
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

            foreach ($employees as $employee) {
                try {

                    // PRESENCE CUTS
                    $presences = Presence::where('employee_id', $employee->id)
                        ->whereBetween('date', [$start, $end])
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
                    $totalCuts = $lateCuts + $leaveCuts;
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
        }

        return back()->with('success', 'Gaji berhasil digenerate');
    }
}
