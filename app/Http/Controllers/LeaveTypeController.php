<?php

namespace App\Http\Controllers;

use App\Models\LeaveType;
use Illuminate\Http\Request;

class LeaveTypeController extends Controller
{
    public function index()
    {
        $leaveTypes = LeaveType::all();

        return view('leave-type.index', compact('leaveTypes'));
    }

    public function create()
    {
        return view('leave-type.create');
    }

    public function store(Request $request)
    {
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

        return redirect()->route('leave-type.index')->with('success', 'Jenis Cuti Telah Dibuat');
    }

    public function edit(LeaveType $leaveType)
    {
        return view('leave-type.edit', compact('leaveType'));
    }

    public function update(Request $request, LeaveType $leaveType)
    {
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

        return redirect()->route('leave-type.index')->with('success', 'Jenis Cuti Telah Diupdate');
    }
}
