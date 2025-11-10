<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;

class LeaveRequestController extends Controller
{
    public function index()
    {
        $leaveRequests = LeaveRequest::all();
        return view('leave.index', compact('leaveRequests'));
    }

    public function create()
    {
        $leaveRequests = LeaveRequest::all();
        $employees = Employee::all();
        return view('leave.create', compact('leaveRequests', 'employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'leave_type' => 'required|string',
        ]);

        $request->merge([
            'status' => 'pending',
        ]);

        LeaveRequest::create($request->all());

        return redirect()->route('leave-request.index')->with('success', 'Data Cuti Berasil Dibuat');
    }

    public function edit(LeaveRequest $leaveRequest)
    {
        $employees = Employee::all();

        return view('leave.edit', compact('employees', 'leaveRequest'));
    }

    public function update(Request $request, LeaveRequest $leaveRequest)
    {
        $request->validate([
            'employee_id' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'leave_type' => 'required|string',
        ]);

        $leaveRequest->update($request->all());

        return redirect()->route('leave-request.index')->with('success', 'Data telah Diubah');
    }

    public function destroy(LeaveRequest $leaveRequest) {
        $leaveRequest->delete();

        return redirect()->route('leave-request.index')->with('success', 'Data Telah Dihapuss');
    }
}
