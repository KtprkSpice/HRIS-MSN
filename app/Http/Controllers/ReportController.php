<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\Presence;
use App\Models\Salary;

class ReportController extends Controller
{
    public function index()
    {
        $startMonth = today()->startOfMonth();
        $endMonth = today()->endOfMonth();
        $activeEmployees = Employee::whereHas('user.role', function ($q) {
            $q->where('name', 'employee');
        })->count();
        $leaveTotal = LeaveRequest::where('status', 'approved')->where(function ($querry) use ($startMonth, $endMonth) {
            $querry->whereBetween('start_date', [$startMonth, $endMonth])
                ->orWhereBetween('end_date', [$startMonth, $endMonth])
                ->orWhere(function ($q) use ($startMonth, $endMonth) {
                    $q->where('start_date', '<=', $startMonth)
                        ->where('end_date', '>=', $endMonth);
                });
        })->count();
        $salaries = Salary::whereNull('deleted_at')->whereIn('date', [$startMonth, $endMonth])->sum('total');
        $employees = Employee::whereHas('user.role', function ($q) {
            $q->whereIn('name', ['employee', 'hr']);
        })->get();

        $absentTotal = Presence::whereHas('employee', function ($q) {
            $q->where('status', 'active');
        })->where('status', 'absent')
            ->count();

        foreach ($employees as $employee) {
            $employee->salary_total = Salary::where('employee_id', $employee->id)
                ->whereBetween('date', [$startMonth, $endMonth])
                ->sum('total');

            $employee->leave_total = LeaveRequest::where('employee_id', $employee->id)
                ->where('status', 'approved')
                ->where(function ($querry) use ($startMonth, $endMonth) {
                    $querry->whereBetween('start_date', [$startMonth, $endMonth])
                        ->orWhereBetween('end_date', [$startMonth, $endMonth])
                        ->orWhere(function ($q) use ($startMonth, $endMonth) {
                            $q->where('start_date', '<=', $startMonth)
                                ->where('end_date', '>=', $endMonth);
                        });
                })->count();

            $employee->absent_total = Presence::where('employee_id', $employee->id)->where('status', 'absent')->count();
        }

        return view('Report.index', compact('activeEmployees', 'leaveTotal', 'salaries', 'employees', 'absentTotal'));
    }
}
