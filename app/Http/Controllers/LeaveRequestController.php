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
        $user = auth()->user();
        $roles = auth()->user()->role->name;

        if ($roles === 'employee') {
            $leaveRequests = LeaveRequest::where('employee_id', $user->employee->id)->get();
        } else {
            $leaveRequests = LeaveRequest::all();
        }

        return view('leave.index', compact('leaveRequests'));
    }

    public function create()
    {

        $roles = auth()->user()->role->name;

        if ($roles === 'employee') {
            abort(403, 'Anda tidak dapat mengakses halaman ini.');
        } else {
            $types = LeaveType::all();
            $leaveRequests = LeaveRequest::all();
            $employees = Employee::all();
        }

        return view('leave.create', compact('leaveRequests', 'employees', 'types'));
    }

    public function store(Request $request)
    {

        $roles = auth()->user()->role->name;

        if ($roles === 'employee') {
            abort(403, 'Anda tidak dapat mengakses halaman ini.');
        } else {
            $validated = $request->validate([
                'employee_id' => 'required',
                'start_date' => 'required|date',
                'end_date' => 'required|date',
                'leave_id' => 'required',
                'document_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            ]);

            // dd(
            //     $request->hasFile('document_file'),
            //     $request->file('document_file'),
            //     $request->file('document_file')?->getPathname()
            // );

            if ($request->file('document_file')) {

                $file = $request->file('document_file');

                $filename = time().'_'.$file->getClientOriginalName();

                $file->move(storage_path('app/public/surat_dokter'), $filename);

                $validated['document_file'] = 'surat_dokter/'.$filename;
            }

            LeaveRequest::create($validated);
        }

        return redirect()->route('leave-request.index')
            ->with('success', 'Data Cuti Berhasil Dibuat');
    }

    public function edit(LeaveRequest $leaveRequest)
    {
        $roles = auth()->user()->role->name;

        if ($roles === 'employee') {
            abort(403, 'Anda tidak dapat mengakses halaman ini.');
        } else {
            $employees = Employee::all();
            $types = LeaveType::all();
        }

        return view('leave.edit', compact('employees', 'leaveRequest', 'types'));
    }

    public function update(Request $request, LeaveRequest $leaveRequest)
    {

        $roles = auth()->user()->role->name;

        if ($roles === 'employee') {
            abort(403, 'Anda tidak dapat mengakses halaman ini.');
        } else {

            $validated = $request->validate([
                'employee_id' => 'required',
                'start_date' => 'required|date',
                'end_date' => 'required|date',
                'leave_id' => 'required',
                'document_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            ]);

            if ($request->file('document_file')) {

                // hapus file lama
                if ($leaveRequest->document_file &&
                    file_exists(storage_path('app/public/'.$leaveRequest->document_file))) {
                    unlink(storage_path('app/public/'.$leaveRequest->document_file));
                }

                // Create file baru
                $file = $request->file('document_file');

                $filename = time().'_'.$file->getClientOriginalName();

                $file->move(storage_path('app/public/surat_dokter'), $filename);

                $validated['document_file'] = 'surat_dokter/'.$filename;
            }

            $leaveRequest->update($validated);
        }

        return redirect()->route('leave-request.index')->with('success', 'Data telah Diubah');
    }

    public function destroy(LeaveRequest $leaveRequest)
    {
        $roles = auth()->user()->role->name;

        if ($roles === 'employee') {
            abort(403, 'Anda tidak dapat mengakses halaman ini.');
        } else {
            $leaveRequest->delete();
        }

        return redirect()->route('leave-request.index')->with('success', 'Data Telah Dihapuss');
    }

    public function pending($id)
    {

        $roles = auth()->user()->role->name;

        if ($roles === 'employee') {
            abort(403, 'Anda tidak dapat mengakses halaman ini.');
        } else {
            $leaveRequest = LeaveRequest::find($id);

            $name = $leaveRequest->employee->fullname;

            $leaveRequest->update([
                'status' => 'pending',
            ]);
        }

        return redirect()->route('leave-request.index')->with('success', "Cuti Untuk $name menjadi pending");
    }

    public function confirmed($id)
    {
        $roles = auth()->user()->role->name;

        if ($roles === 'employee') {
            abort(403, 'Anda tidak dapat mengakses halaman ini.');
        } else {
            $leaveRequest = LeaveRequest::find($id);

            $name = $leaveRequest->employee->fullname;

            $leaveRequest->update([
                'status' => 'confirmed',
            ]);
        }

        return redirect()->route('leave-request.index')->with('success', "Cuti untuk $name menjadi confirmed");
    }

    public function rejected($id)
    {

        $roles = auth()->user()->role->name;

        if ($roles === 'employee') {
            abort(403, 'Anda tidak dapat mengakses halaman ini.');
        } else {
            $leaveRequest = LeaveRequest::find($id);

            $name = $leaveRequest->employee->fullname;

            $leaveRequest->update([
                'status' => 'rejected',
            ]);
        }

        return redirect()->route('leave-request.index')->with('success', "Cuti untuk $name telah diupdate menjadi rejected");
    }
}
