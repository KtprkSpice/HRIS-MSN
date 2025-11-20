<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Salary;
use Illuminate\Http\Request;

class SalaryController extends Controller
{
    public function index()
    {
        $salaries = Salary::all();

        return view('Salary.index', compact('salaries'));
    }

    public function create()
    {
        $employees = Employee::all();
        return view('salary.create', compact('employees'));
    }

    public function store(Request $request)
    {
       $request->validate([
            'employee_id' => 'required',
            'net_salary' => 'required',
            'cuts' => 'nullable',
            'bonus' => 'nullable',
            'date' => 'nullable',
        ]);

        $salary = (int) str_replace(".", '', $request->net_salary);
        $bonus = (int) str_replace(".", '', $request->bonus);
        $cuts = (int) str_replace(".", '', $request->cuts);

        $request->merge([
            'net_salary' => $salary,
            'bonus' => $bonus,
            'cuts' => $cuts,
            'total' => $salary + $bonus - $cuts
        ]);

        Salary::create($request->all());
        return redirect()->route('salary.index')->with('success', 'Data Berhasil ditambahkan');
    }

    public function edit(Salary $salary) {
        $employees = Employee::all();
        return view('salary.edit',compact('salary', 'employees'));
    }

    public function update(Request $request, Salary $salary) {

        $request->validate([
            'employee_id' => 'required',
            'net_salary' => 'required',
            'cuts' => 'nullable',
            'bonus' => 'nullable',
            'date' => 'nullable',
        ]);

        $net = (int) str_replace(".", '', $request->net_salary);
        $cuts = (int) str_replace(".", '', $request->cuts);
        $bonus = (int) str_replace(".", '', $request->bonus);


        $request->merge([
            'net_salary' => $net,
            'bonus' => $bonus,
            'cuts' => $cuts,
            'total' => $net + $bonus - $cuts,
        ]);

        $salary->update($request->all());

        return redirect()->route('salary.index')->with('success', 'Data Telah diubah');
    }

    public function destroy (Salary $salary) {
        $salary->delete();

        return redirect()->route('salary.index')->with('success', 'Data Telah Dihapus');
    }
}
