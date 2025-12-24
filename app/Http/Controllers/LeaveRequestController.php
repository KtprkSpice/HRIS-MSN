<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
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
        $types = LeaveType::all();
        $leaveRequests = LeaveRequest::all();
        $employees = Employee::all();

        return view('leave.create', compact('leaveRequests', 'employees', 'types'));
    }

    public function store(Request $request)
    {

        $request->validate([
            'employee_id' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'leave_id' => 'required',
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
            'leave_id' => 'required',
        ]);

        $leaveRequest->update($request->all());

        return redirect()->route('leave-request.index')->with('success', 'Data telah Diubah');
    }

    public function destroy(LeaveRequest $leaveRequest)
    {
        $leaveRequest->delete();

        return redirect()->route('leave-request.index')->with('success', 'Data Telah Dihapuss');
    }

    public function pending($id)
    {
        $leaveRequest = LeaveRequest::find($id);

        $name = $leaveRequest->employee->fullname;

        $leaveRequest->update([
            'status' => 'pending',
        ]);

        return redirect()->route('leave-request.index')->with('success', "Cuti Untuk $name menjadi pending");
    }

    public function confirmed($id)
    {
        $leaveRequest = LeaveRequest::find($id);

        $name = $leaveRequest->employee->fullname;

        $leaveRequest->update([
            'status' => 'confirmed',
        ]);

        return redirect()->route('leave-request.index')->with('success', "Cuti untuk $name menjadi confirmed");
    }

    public function rejected($id)
    {
        $leaveRequest = LeaveRequest::find($id);

        $name = $leaveRequest->employee->fullname;

        $leaveRequest->update([
            'status' => 'rejected',
        ]);

        return redirect()->route('leave-request.index')->with('success', "Cuti untuk $name telah diupdate menjadi rejected");
    }
}
