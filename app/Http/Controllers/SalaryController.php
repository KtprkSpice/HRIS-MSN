<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Salary;
use Illuminate\Http\Request;

class SalaryController extends Controller
{
    public function index() {
        $salaries = Salary::all();

        return view('Salary.index', compact('salaries'));
    }

    public function create() {
        $employees = Employee::all();
        return view('salary.create', compact('employees'));
    }
}
