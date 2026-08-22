<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Models\Employee;
use App\Models\Position;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $roles = auth()->user()->role->name;

        // Employee Accsess
        if ($roles === 'employee') {
            abort(403, "Anda Tidak memiliki akses");
        }

        $excludedRole = match ($roles) {
            'owner' => ['owner'],
            'hr' => ['hr', 'owner'],
            default => ['hr', 'owner'],
        };

        $employees = Employee::with(['division', 'position'])
            ->whereHas('user.role', function ($q) use ($excludedRole) {
                $q->WhereNotIn('name', $excludedRole);
            })
            ->get();

        $countTotalEmployee = $employees->count();

        $countActiveEmployee = $employees->where('status', 'active')->count();

        $countNonActiveEmployee = $employees->where('status', 'inactive')->count();

        $divisions = Division::where('status', 'active')->get();

        return view('Employees.index', compact('employees', 'countActiveEmployee', 'countNonActiveEmployee', 'countTotalEmployee', 'divisions'));
    }

    public function show(Employee $employee)
    {
        return view('Employees.show', compact('employee'));
    }

    public function create()
    {
        $user = auth()->user();
        $roles = auth()->user()->role->name;

        if ($roles === 'employee') {
            abort(403);
        } else {
            $divisions = Division::where('status', 'active');
            $positions = Position::all();
            $roles = Role::all();
        }

        return view('Employees.create', compact('divisions', 'roles', 'positions'));
    }

    public function store(Request $request)
    {
        $roles = auth()->user()->role->name;

        if ($roles === 'employee' || empty($roles)) {
            abort(403);
        }

        $validated = $request->validate([
            'fullname' => 'required|string|max:255',
            'nik' => 'required|digits_between:1,20|unique:employees,nik',
            'position_id' => 'required',
            'address' => 'nullable|string',
            'gender' => 'required|string',
            'email' => 'required|unique:employees,email|string',
            'phone' => 'required|unique:employees,phone|digits_between:1,20|max:20',
            'hire_date' => 'required|date',
            'born_date' => 'required|date',
            'bpjs_kesehatan' => 'nullable|digits_between:1,20|max:20|unique:employees,bpjs_kesehatan',
            'bpjs_ketenagakerjaan' => 'nullable|digits_between:1,20|max:20|unique:employees,bpjs_ketenagakerjaan',
            'npwp' => 'required|max:30|unique:employees,npwp',
            'role_id' => 'required|exists:roles,id',
        ]);

        $position = Position::findOrFail($validated['position_id']);
        $division = $position->division_id;

        if ($roles === 'hr') {
            $employeeRole = Role::where('name', 'employee')->firstOrFail();

            if (! ($employeeRole)) {
                return redirect()->back()->withErrors(['error' => 'Role Employee tidak ada']);
            }
        }

        // Owner else ($roles === 'owner') {
        DB::transaction(function () use ($validated, $division) {
            $user = User::create([
                'name' => $validated['fullname'],
                'email' => strtolower(str_replace(' ', '', $validated['email'])),
                'password' => Hash::make(strtolower(str_replace(' ', '', $validated['email']))),
                'role_id' => $validated['role_id'],
            ]);

            Employee::create([
                'fullname' => $validated['fullname'],
                'nik' => $validated['nik'],
                'position_id' => $validated['position_id'],
                'division_id' => $division,
                'address' => $validated['address'] ?? null,
                'gender' => $validated['gender'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'hire_date' => $validated['hire_date'],
                'born_date' => $validated['born_date'],
                'bpjs_kesehatan' => $validated['bpjs_kesehatan'],
                'bpjs_ketenagakerjaan' => $validated['bpjs_ketenagakerjaan'],
                'npwp' => $validated['npwp'],
                'status' => 'active',
                'user_id' => $user->id,
            ]);
        });

        return redirect()->route('employee.index')->with('success', 'Data Berhasil Dibuat');
    }

    public function edit(Employee $employee)
    {
        $roles = auth()->user()->role->name;

        if ($roles === 'employee') {
            abort(403);
        } else {
            $divisions = Division::where('status', 'active');
            $roles = Role::all();
            $user = User::all();
            $positions = Position::all();
        }

        return view('Employees.edit', compact('divisions', 'roles', 'employee', 'user', 'positions'));
    }

    public function update(Request $request, Employee $employee)
    {
        $nama = $employee->fullname;
        $roles = auth()->user()->role->name;

        $validated = $request->validate([
            'fullname' => 'required|string|max:255',
            'nik' => 'required|digits_between:1,20',
            'position_id' => 'required',
            'gender' => 'required|string',
            'address' => 'nullable|string',
            'gender' => 'required|string',
            'email' => 'required|string',
            'phone' => 'required|digits_between:1,20|max:20',
            'hire_date' => 'required|date',
            'born_date' => 'required|date',
            'bpjs_kesehatan' => 'nullable|digits_between:1,20|max:20',
            'bpjs_ketenagakerjaan' => 'nullable|digits_between:1,20|max:20',
            'npwp' => 'required|max:30',
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|string|max:255',

        ]);

        $position = Position::findOrFail($validated['position_id']);
        $division = $position->division_id;

        if ($roles === 'employee' || empty($roles)) {
            abort(403);
        }

        if ($roles === 'hr') {
            $employeeRole = Role::where('name', 'employee')->firstOrFail();

            if (! ($employeeRole)) {
                return redirect()->back()->withErrors(['error' => 'Role Employee tidak ada']);
            }
        }

        $employee->update([
            'fullname' => $validated['fullname'],
            'nik' => $validated['nik'],
            'position_id' => $validated['position_id'],
            'division_id' => $division,
            'address' => $validated['address'] ?? null,
            'gender' => $validated['gender'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'hire_date' => $validated['hire_date'],
            'born_date' => $validated['born_date'],
            'bpjs_kesehatan' => $validated['bpjs_kesehatan'],
            'bpjs_ketenagakerjaan' => $validated['bpjs_ketenagakerjaan'],
            'npwp' => $validated['npwp'],
            'status' => $validated['status'],
        ]);

        if ($employee->user) {
            $employee->user->update([
                'role_id' => $validated['role_id'],
            ]);
        }

        return redirect()->route('employee.index')->with('success', "Data $nama Telah Diubah");
    }

    public function destroy(Employee $employee)
    {
        $roles = auth()->user()->role->name;
        $nama = $employee->fullname;
        if ($roles === 'employee') {
            abort(403);
        } else {
            $employee->delete();
        }

        return redirect()
            ->route('employee.index')
            ->with('success', "Data $nama Berhasil dihapus");
    }
}
