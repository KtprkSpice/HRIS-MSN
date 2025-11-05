<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Models\Employee;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::all();

        return view('Employees.index', compact('employees'));
    }

    public function create()
    {
        $divisions = Division::all();
        $roles = Role::all();
        return view('Employees.create', compact('divisions', 'roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'fullname' => 'required|string|max:255',
            'nik' => 'required|digits_between:1,20|unique:employees,nik',
            'division_id' => 'required',
            'address' => 'nullable|string',
            'email' => 'required|unique:employees,email|string',
            'phone' => 'required|unique:employees,phone|digits_between:1,20|max:20',
            'hire_date' => 'required|date',
            'born_date' => 'required|date',
            'bpjs_kesehatan' => 'required|digits_between:1,20|max:20|unique:employees,bpjs_kesehatan',
            'bpjs_ketenagakerjaan' => 'required|digits_between:1,20|max:20|unique:employees,bpjs_ketenagakerjaan',
            'npwp' => 'required|digits_between:1,20|max:20|unique:employees,npwp',
        ]);

        DB::transaction(function () use ($validated) {
            $user = User::create([
                'name' => $validated['fullname'],
                'email' => strtolower(str_replace(' ', '', $validated['fullname'] . '@swatservice.com')),
                'password' => Hash::make(strtolower(str_replace(' ', '', $validated['fullname'] . '@swatservice.com'))),
            ]);

            Employee::create([
                'fullname' => $validated['fullname'],
                'nik' => $validated['nik'],
                'division_id' => $validated['division_id'],
                'address' => $validated['address'] ?? null,
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'hire_date' => $validated['hire_date'],
                'born_date' => $validated['born_date'],
                'bpjas_kesehatan' => $validated['bpjs_kesehatan'],
                'bpjs_ketenagakerjaan' => $validated['bpjs_ketenagakerjaan'],
                'npwp' => $validated['npwp'],
                'status' => 'active',
                'user_id' => $user->id,
            ]);
        });

        return redirect()->route('employee.index')->with('success', 'Data Berhasil Dibuat');
    }
}
