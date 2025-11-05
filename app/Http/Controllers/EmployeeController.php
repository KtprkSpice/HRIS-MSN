<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index() {
        $employees = Employee::all();

        return view('Employees.index', compact('employees'));
    }

    public function create() {

        
        return view('Employees.create');
    }
}
