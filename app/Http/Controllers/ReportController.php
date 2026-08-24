<?php

namespace App\Http\Controllers;

use App\Exports\EmployeeReportExport;
use App\Models\Division;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\Presence;
use App\Models\Salary;
use App\Support\AttendancePolicy;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        [$startMonth, $endMonth, $selectedMonth, $selectedYear] = $this->reportPeriod($request);

        $activeEmployees = Employee::whereHas('user.role', function ($q) {
            $q->where('name', 'employee');
        })->count();
        $leaveTotal = LeaveRequest::whereIn('status', AttendancePolicy::APPROVED_LEAVE_STATUSES)->where(function ($querry) use ($startMonth, $endMonth) {
            $querry->whereBetween('start_date', [$startMonth, $endMonth])
                ->orWhereBetween('end_date', [$startMonth, $endMonth])
                ->orWhere(function ($q) use ($startMonth, $endMonth) {
                    $q->where('start_date', '<=', $startMonth)
                        ->where('end_date', '>=', $endMonth);
                });
        })->count();
        $salaries = Salary::whereNull('deleted_at')->whereBetween('date', [$startMonth, $endMonth])->sum('total');
        $employees = Employee::whereHas('user.role', function ($q) {
            $q->whereIn('name', ['employee', 'hr']);
        })->where('status', 'active')->with(['division', 'tasks'])->get();

        $absentTotal = Presence::whereHas('employee', function ($q) {
            $q->where('status', 'active');
        })->where('status', 'absent')
            ->whereBetween('date', [$startMonth, $endMonth])
            ->count();

        $divisions = Division::where('status', 'active')->get();

        foreach ($employees as $employee) {
            $employee->salary_total = Salary::where('employee_id', $employee->id)
                ->whereBetween('date', [$startMonth, $endMonth])
                ->sum('total');

            $employee->leave_total = LeaveRequest::where('employee_id', $employee->id)
                ->whereIn('status', AttendancePolicy::APPROVED_LEAVE_STATUSES)
                ->where(function ($querry) use ($startMonth, $endMonth) {
                    $querry->whereBetween('start_date', [$startMonth, $endMonth])
                        ->orWhereBetween('end_date', [$startMonth, $endMonth])
                        ->orWhere(function ($q) use ($startMonth, $endMonth) {
                            $q->where('start_date', '<=', $startMonth)
                                ->where('end_date', '>=', $endMonth);
                        });
                })->count();

            $employee->absent_total = Presence::where('employee_id', $employee->id)
                ->where('status', 'absent')
                ->whereBetween('date', [$startMonth, $endMonth])
                ->count();
        }

        return view('Report.index', compact(
            'activeEmployees',
            'leaveTotal',
            'salaries',
            'employees',
            'absentTotal',
            'divisions',
            'selectedMonth',
            'selectedYear'
        ));
    }

    public function export(Request $request)
    {
        [$startMonth, $endMonth, $selectedMonth, $selectedYear] = $this->reportPeriod($request);

        $employees = Employee::whereHas('user.role', function ($q) {
            $q->whereIn('name', ['employee', 'hr']);
        })->where('status', 'active')->with('division')->get();

        foreach ($employees as $employee) {
            $employee->salary_total = Salary::where('employee_id', $employee->id)
                ->whereBetween('date', [$startMonth, $endMonth])
                ->sum('total');

            $employee->leave_total = LeaveRequest::where('employee_id', $employee->id)
                ->whereIn('status', AttendancePolicy::APPROVED_LEAVE_STATUSES)
                ->where(function ($querry) use ($startMonth, $endMonth) {
                    $querry->whereBetween('start_date', [$startMonth, $endMonth])
                        ->orWhereBetween('end_date', [$startMonth, $endMonth])
                        ->orWhere(function ($q) use ($startMonth, $endMonth) {
                            $q->where('start_date', '<=', $startMonth)
                                ->where('end_date', '>=', $endMonth);
                        });
                })->count();

            $employee->absent_total = Presence::where('employee_id', $employee->id)
                ->where('status', 'absent')
                ->whereBetween('date', [$startMonth, $endMonth])
                ->count();
        }

        $fileName = 'Laporan_Karyawan_'.$selectedMonth.'-'.$selectedYear.'_'.now()->format('d-m-Y_H-i-s').'.xlsx';

        return Excel::download(new EmployeeReportExport($employees), $fileName);
    }

    private function reportPeriod(Request $request): array
    {
        $selectedMonth = (int) $request->query('month', now()->month);
        $selectedYear = (int) $request->query('year', now()->year);

        if ($selectedMonth < 1 || $selectedMonth > 12) {
            $selectedMonth = now()->month;
        }

        if ($selectedYear < 2023 || $selectedYear > now()->year + 1) {
            $selectedYear = now()->year;
        }

        $period = Carbon::create($selectedYear, $selectedMonth, 1);

        return [
            $period->copy()->startOfMonth(),
            $period->copy()->endOfMonth(),
            str_pad((string) $selectedMonth, 2, '0', STR_PAD_LEFT),
            (string) $selectedYear,
        ];
    }
}
