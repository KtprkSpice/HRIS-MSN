<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\leaveApproval;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $user = auth()->user();
        $roles = auth()->user()->role->name;

        $types = LeaveType::all();
        $employees = null;

        if ($roles === 'employee') {
            $employees = Employee::where('user_id', $user->id)->get();
        } elseif ($roles === 'hr') {
            // Bisa ajuin cuti untuk semua employe kecuali diri sendiri
            $employees = Employee::where('user_id', '!=', $user->id)->get();
        } else {
            $employees = Employee::all();
        }

        return view('leave.create', compact('employees', 'types'));
    }

    public function store(Request $request)
    {

        $user = auth()->user();
        $roles = auth()->user()->role->name;

        if ($roles === 'employee') {

            $validated = $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date',
                'leave_id' => 'required',
                'document_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            ]);

            DB::transaction(function () use ($user, $request, $validated) {

                if ($request->file('document_file')) {

                    $file = $request->file('document_file');

                    $filename = time().'_'.$file->getClientOriginalName();

                    $file->move(storage_path('app/public/surat_dokter'), $filename);

                    $validated['document_file'] = 'surat_dokter/'.$filename;
                }

                $leave = LeaveRequest::create([
                    ...$validated,
                    'employee_id' => $user->employee->id,
                ]);

                $hrRole = Role::where('name', 'hr')->first();
                $ownerRole = Role::where('name', 'owner')->first();

                // Hr Approval
                leaveApproval::create([
                    'leave_request_id' => $leave->id,
                    'approval_order' => 1,
                    'role_id' => $hrRole->id,
                    'status' => 'pending',
                ]);

                // Owner approval
                leaveApproval::create([
                    'leave_request_id' => $leave->id,
                    'approval_order' => 2,
                    'role_id' => $ownerRole->id,
                    'status' => 'pending',
                ]);
            });

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

            DB::transaction(function () use ($request, $validated) {
                if ($request->file('document_file')) {

                    $file = $request->file('document_file');

                    $filename = time().'_'.$file->getClientOriginalName();

                    $file->move(storage_path('app/public/surat_dokter'), $filename);

                    $validated['document_file'] = 'surat_dokter/'.$filename;
                }
                $leave = LeaveRequest::create([
                    ...$validated,
                    'status' => 'pending',
                    'current_step' => 1,
                    'final_appoved_at' => null,
                ]);

                $hrRole = Role::where('name', 'hr')->first();
                $ownerRole = Role::where('name', 'owner')->first();

                // Hr Approval
                leaveApproval::create([
                    'leave_request_id' => $leave->id,
                    'approval_order' => 1,
                    'role_id' => $hrRole->id,
                    'status' => 'pending',
                ]);

                // Owener Approval
                leaveApproval::create([
                    'leave_request_id' => $leave->id,
                    'approval_order' => 2,
                    'role_id' => $ownerRole->id,
                    'status' => 'pending',
                ]);

            });
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

    // Approved
    public function approve($id)
    {
        $user = auth()->user();
        $role = $user->role->name;

        if (! in_array($role, ['hr', 'owner'])) {
            abort(403, 'Anda tidak memiliki akses.');
        }

        $leave = LeaveRequest::with('approvals')->findOrFail($id);

        DB::transaction(function () use ($leave, $user, $role) {

            // 🔹 Cari approval step sesuai current_step
            $approval = leaveApproval::where('leave_request_id', $leave->id)
                ->where('approval_order', $leave->current_step)
                ->first();

            // dd(
            //     'Current Step: '.$leave->current_step,
            //     'Approval Role ID: '.$approval?->role_id,
            //     'Approval Role Name: '.$approval?->role?->name,
            //     'Login Role ID: '.auth()->user()->role_id,
            //     'Login Role Name: '.auth()->user()->role->name
            // );

            // 🔹 Pastikan role sesuai
            if ($approval->role->name !== $role) {
                abort(403, 'Bukan giliran Anda untuk approve.');
            }

            // 🔹 Update approval step
            $approval->update([
                'status' => 'approved',
                'approved_by' => $user->id,
                'approved_at' => now(),
            ]);

            // 🔹 Kalau masih ada step berikutnya
            $nextStep = leaveApproval::where('leave_request_id', $leave->id)
                ->where('approval_order', '>', $leave->current_step)
                ->orderBy('approval_order')
                ->first();

            if ($nextStep) {

                // Lanjut ke step berikutnya
                $leave->update([
                    'current_step' => $nextStep->approval_order,
                ]);

            } else {

                // Tidak ada step lagi → FINAL APPROVED
                $leave->update([
                    'status' => 'approved',
                    'final_approved_at' => now(),
                ]);
            }
        });

        return redirect()->route('leave-request.index')
            ->with('success', 'Cuti berhasil diapprove.');
    }

    // Reject
    public function rejected($id)
    {
        $user = auth()->user();
        $role = $user->role->name;

        if (! in_array($role, ['hr', 'owner'])) {
            abort(403, 'Anda tidak memiliki akses.');
        }

        $leave = LeaveRequest::with('approvals')->findOrFail($id);

        DB::transaction(function () use ($leave, $user, $role) {

            // 🔹 Cari approval step sesuai current_step
            $approval = leaveApproval::where('leave_request_id', $leave->id)
                ->where('approval_order', $leave->current_step)
                ->first();

            // dd(
            //     'Current Step: '.$leave->current_step,
            //     'Approval Role ID: '.$approval?->role_id,
            //     'Approval Role Name: '.$approval?->role?->name,
            //     'Login Role ID: '.auth()->user()->role_id,
            //     'Login Role Name: '.auth()->user()->role->name
            // );

            // 🔹 Pastikan role sesuai
            if ($approval->role->name !== $role) {
                abort(403, 'Bukan giliran Anda untuk approve.');
            }

            // 🔹 Update approval step
            $approval->update([
                'status' => 'rejected',
                'approved_by' => $user->id,
                'approved_at' => now(),
            ]);

            // Tidak ada step lagi → FINAL APPROVED
            $leave->update([
                'status' => 'rejected',
                'final_approved_at' => now(),
            ]);
        });

        return redirect()->route('leave-request.index')
            ->with('success', 'Cuti berhasil diapprove.');
    }
}
