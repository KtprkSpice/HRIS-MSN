<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\Salary;

class ReportController extends Controller
{
    public function index()
    {
        $startMonth = today()->startOfMonth();
        $endMonth = today()->endOfMonth(28);
        $activeEmployees = Employee::where('status', 'active')->count();
        $leaveTotal = LeaveRequest::where('status', 'approved')->count();
        $salaries = Salary::whereNull('deleted_at')->whereIn('date', [$startMonth, $endMonth])->sum('total');
        $employees = Employee::all();

        return view('Report.index', compact('activeEmployees', 'leaveTotal', 'salaries', 'employees'));
    }
}
