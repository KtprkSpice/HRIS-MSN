<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\Salary;

class ReportController extends Controller
{
    public function index()
    {
        $activeEmployees = Employee::where('status', 'active')->count();
        $leaveTotal = LeaveRequest::where('status', 'approved')->count();
        $salaries = Salary::whereNull('deleted_at')->sum('total');
        $employees = Employee::all();

        return view('Report.index', compact('activeEmployees', 'leaveTotal', 'salaries', 'employees'));
    }
}
