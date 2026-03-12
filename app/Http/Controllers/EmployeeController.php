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
        $user = auth()->user();
        $roles = auth()->user()->role->name;

        if ($roles === 'employee') {
            abort(403, 'Anda tidak memiliki akses ke halaman ini');
        } elseif ($roles === 'owner') {
            // Owner
            $employees = Employee::whereHas('user.role', function ($q) {
                $q->where('name', '!=', 'owner');
            })->get();
            // $employeeStats = Employee::whereHas('user.role', function ($q) {
            //     $q->where('name', 'employee');
            // })->selectRaw('status, COUNT(*) as total')
            //     ->groupBy('status')
            //     ->pluck('total', 'status');

            $countActiveEmployee = $employees->where('status', 'active')->count();
            $countNonActiveEmployee = $employees->where('status', 'inactive')->count();
            $countTotalEmployee = $employees->count();

        } else {
            // Hr
            $employees = Employee::whereHas('user.role', function ($q) {
                $q->whereNotIn('name', ['owner', 'hr']);
            })->get();
            // $employeeStats = Employee::whereHas('user.role', function ($q) {
            //     $q->where('name', 'employee');
            // })->selectRaw('status, COUNT(*) as total')
            //     ->groupBy('status')
            //     ->pluck('total', 'status');

            $countActiveEmployee = $employees->where('status', 'active')->count();
            $countNonActiveEmployee = $employees->where('status', 'inactive')->count();
            $countTotalEmployee = $employees->count();

        }

        return view('Employees.index', compact('employees', 'countActiveEmployee', 'countNonActiveEmployee', 'countTotalEmployee'));

    }

    public function create()
    {
        $user = auth()->user();
        $roles = auth()->user()->role->name;

        if ($roles === 'employee') {
            abort(403);
        } else {
            $divisions = Division::all();
            $roles = Role::all();
        }

        return view('Employees.create', compact('divisions', 'roles'));
    }

    public function store(Request $request)
    {
        $roles = auth()->user()->role->name;

        if ($roles === 'employee') {
            abort(403);
        } else {
            $validated = $request->validate([
                'fullname' => 'required|string|max:255',
                'nik' => 'required|digits_between:1,20|unique:employees,nik',
                'division_id' => 'required',
                'address' => 'nullable|string',
                'email' => 'required|unique:employees,email|string',
                'phone' => 'required|unique:employees,phone|digits_between:1,20|max:20',
                'hire_date' => 'required|date',
                'born_date' => 'required|date',
                'bpjs_kesehatan' => 'nullable|digits_between:1,20|max:20|unique:employees,bpjs_kesehatan',
                'bpjs_ketenagakerjaan' => 'nullable|digits_between:1,20|max:20|unique:employees,bpjs_ketenagakerjaan',
                'npwp' => 'required|max:30|unique:employees,npwp',
                'role_id' => 'required|exists:roles,id',
            ]);

            DB::transaction(function () use ($validated) {
                $user = User::create([
                    'name' => $validated['fullname'],
                    'email' => strtolower(str_replace(' ', '', $validated['email'])),
                    'password' => Hash::make(strtolower(str_replace(' ', '', $validated['email']))),
                    'role_id' => $validated['role_id'],
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
                    'bpjs_kesehatan' => $validated['bpjs_kesehatan'],
                    'bpjs_ketenagakerjaan' => $validated['bpjs_ketenagakerjaan'],
                    'npwp' => $validated['npwp'],
                    'status' => 'active',
                    'user_id' => $user->id,
                ]);
            });
        }

        return redirect()->route('employee.index')->with('success', 'Data Berhasil Dibuat');
    }

    public function edit(Employee $employee)
    {
        $roles = auth()->user()->role->name;

        if ($roles === 'employee') {
            abort(403);
        } else {
            $divisions = Division::all();
            $roles = Role::all();
            $user = User::all();
        }

        return view('Employees.edit', compact('divisions', 'roles', 'employee', 'user'));
    }

    public function update(Request $request, Employee $employee)
    {
        $nama = $employee->fullname;
        $roles = auth()->user()->role->name;

        if ($roles === 'employee') {
            abort(403);
        } else {
            $request->validate([
                'fullname' => 'required|string|max:255',
                'nik' => 'required|digits_between:1,20',
                'division_id' => 'required',
                'address' => 'nullable|string',
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

            $employee->update([
                'fullname' => $request->fullname,
                'nik' => $request->nik,
                'division_id' => $request->division_id,
                'address' => $request->address,
                'email' => $request->email,
                'phone' => $request->phone,
                'hire_date' => $request->hire_date,
                'born_date' => $request->born_date,
                'bpjs_kesehatan' => $request->bpjs_kesehatan,
                'bpjs_ketenagakerjaan' => $request->bpjs_ketenagakerjaan,
                'npwp' => $request->npwp,
                'status' => $request->status,
            ]);

            if ($employee->user) {
                $employee->user->update([
                    'role_id' => $request->role_id,
                ]);
            }
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
            ->with('success', "Data $nama  Berhasil dihapus");
    }
}
