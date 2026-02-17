<?php

namespace App\Http\Controllers;

use App\Models\LeaveType;
use Illuminate\Http\Request;

class LeaveTypeController extends Controller
{
    public function index()
    {
        $roles = auth()->user()->role->name;

        if (in_array($roles, ['hr', 'owner'])) {
            $leaveTypes = LeaveType::all();

        } else {
            abort(403, 'Anda tidak dapat mengakses halaman ini.');
        }

        return view('leave-type.index', compact('leaveTypes'));

    }

    public function create()
    {
        $roles = auth()->user()->role->name;

        if (in_array($roles, ['hr', 'owner'])) {
            return view('leave-type.create');
        } else {
            abort(403, 'Anda tidak dapat mengakses halaman ini.');
        }

    }

    public function store(Request $request)
    {

        $roles = auth()->user()->role->name;

        if (in_array($roles, ['hr', 'owner'])) {
            $validated = $request->validate([
                'name' => 'string|required|max:255',
                'is_paid' => 'boolean|required',
                'max_days' => 'nullable|numeric|min:1',
                'deduction' => 'nullable|numeric|min:0',
                'document' => 'boolean|required',
            ]);

            if ($validated['is_paid']) {
                $validated['deduction'] = 0;
            }

            if (! $validated['is_paid'] && empty($validated['deduction'])) {
                return back()
                    ->withErrors(['deduction' => 'Potongan wajib diisi untuk cuti tidak dibayar'])
                    ->withInput();
            }

            LeaveType::create($validated);
        } else {
            abort(403, 'Anda tidak dapat mengakses halaman ini.');
        }

        return redirect()->route('leave-type.index')->with('success', 'Jenis Cuti Telah Dibuat');
    }

    public function edit(LeaveType $leaveType)
    {
        $roles = auth()->user()->role->name;

        if (in_array($roles, ['hr', 'owner'])) {
            return view('leave-type.edit', compact('leaveType'));
        } else {
            abort(403, 'Anda tidak dapat mengakses halaman ini.');
        }
    }

    public function update(Request $request, LeaveType $leaveType)
    {

        $roles = auth()->user()->role->name;

        if (in_array($roles, ['hr', 'owner'])) {

            $validated = $request->validate([
                'name' => 'string|required|max:255',
                'is_paid' => 'boolean|required',
                'max_days' => 'nullable|numeric|min:1',
                'deduction' => 'nullable|numeric',
                'document' => 'boolean|required',
            ]);

            if ($validated['is_paid']) {
                $validated['deduction'] = 0;
            }

            if (! $validated['is_paid'] && empty($validated['deduction'])) {
                return back()
                    ->withErrors(['deduction' => 'Potongan wajib diisi untuk cuti tidak dibayar'])
                    ->withInput();
            }

            $leaveType->update($validated);
        } else {
            abort(403, 'Anda tidak dapat mengakses halaman ini.');
        }

        return redirect()->route('leave-type.index')->with('success', 'Jenis Cuti Telah Diupdate');
    }

    public function destroy(LeaveType $leaveType)
    {

        $roles = auth()->user()->role->name;

        if (in_array($roles, ['hr', 'owner'])) {
            $leaveType->delete();
        } else {
            abort(403, 'Anda tidak dapat mengakses halaman ini.');
        }

        return redirect()->route('leave-type.index')->with('success', 'Jenis Cuti Telah Dihapus');
    }
}
