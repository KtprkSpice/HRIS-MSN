<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\leaveApproval;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\Role;
use App\Support\AttendancePolicy;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LeaveRequestController extends Controller
{
    private function leaveStatisticsPayload($employees, ?LeaveRequest $excludeLeaveRequest = null): array
    {
        $employeeIds = $employees->pluck('id');

        $approvedLeaves = LeaveRequest::select('id', 'employee_id', 'leave_id', 'start_date', 'end_date')
            ->whereIn('employee_id', $employeeIds)
            ->whereIn('status', AttendancePolicy::APPROVED_LEAVE_STATUSES)
            ->when($excludeLeaveRequest, function ($query) use ($excludeLeaveRequest) {
                $query->where('id', '!=', $excludeLeaveRequest->id);
            })
            ->get()
            ->map(function ($leave) {
                $start = Carbon::parse($leave->start_date);
                $end = Carbon::parse($leave->end_date);

                return [
                    'employee_id' => (string) $leave->employee_id,
                    'leave_id' => (string) $leave->leave_id,
                    'year' => $start->year,
                    'month' => $start->month,
                    'days' => $start->diffInDays($end) + 1,
                ];
            })
            ->values();

        return [
            'types' => LeaveType::all()
                ->mapWithKeys(function ($type) {
                    return [
                        (string) $type->id => [
                            'limit_type' => $type->limit_type,
                            'limit_days' => $type->limit_days,
                        ],
                    ];
                }),
            'approved_leaves' => $approvedLeaves,
        ];
    }

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

        $leaveStats = $this->leaveStatisticsPayload($employees);
        $defaultEmployeeId = $roles === 'employee' ? optional($employees->first())->id : null;

        return view('leave.create', compact('employees', 'types', 'leaveStats', 'defaultEmployeeId'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $role = $user->role->name;

        $rules = [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'leave_id' => 'required|exists:leave_types,id',
            'document_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ];

        if ($role !== 'employee') {
            $rules['employee_id'] = 'required|exists:employees,id';
        }

        $validated = $request->validate($rules);

        // Tentukan employee
        $employeeId = $role === 'employee'
            ? $user->employee->id
            : $request->employee_id;

        $leaveType = LeaveType::findOrFail($request->leave_id);

        if ($leaveType->document && ! $request->hasFile('document_file')) {
            return back()
                ->withInput()
                ->withErrors(['document_file' => 'Jenis cuti ini wajib melampirkan dokumen.']);
        }

        $start = Carbon::parse($request->start_date);
        $end = Carbon::parse($request->end_date);

        $daysRequested = $start->diffInDays($end) + 1;

        if (
            ! AttendancePolicy::isSuddenLeaveType($leaveType)
            && AttendancePolicy::scheduleOverlapQuery($employeeId, $start, $end)->exists()
        ) {
            return back()
                ->withInput()
                ->with('error', 'Cuti tidak bisa diajukan karena jadwal pada tanggal tersebut sudah digenerate. Gunakan jenis cuti dadakan jika memang mendadak.');
        }

        // ===============================
        // 1️⃣ VALIDASI MAX PER PENGAJUAN
        // ===============================

        if ($leaveType->max_days && $daysRequested > $leaveType->max_days) {
            return back()->with(
                'error',
                'Maksimal pengajuan ' . $leaveType->max_days . ' hari.'
            );
        }

        // ===============================
        // 2️⃣ VALIDASI KUOTA PERIODE
        // ===============================

        if ($leaveType->limit_days && $leaveType->limit_type) {

            $query = LeaveRequest::where('employee_id', $employeeId)
                ->where('leave_id', $leaveType->id)
                ->whereIn('status', AttendancePolicy::APPROVED_LEAVE_STATUSES);

            if ($leaveType->limit_type === 'yearly') {
                $query->whereYear('start_date', $start->year);
            }

            if ($leaveType->limit_type === 'monthly') {
                $query->whereYear('start_date', $start->year)
                    ->whereMonth('start_date', $start->month);
            }

            $usedDays = $query->sum(
                \DB::raw('DATEDIFF(end_date, start_date) + 1')
            );

            if (($usedDays + $daysRequested) > $leaveType->limit_days) {

                $remaining = $leaveType->limit_days - $usedDays;

                return back()->with(
                    'error',
                    'Sisa cuti hanya ' . $remaining . ' hari.'
                );
            }
        }

        // ===============================
        // 3️⃣ SIMPAN DATA
        // ===============================

        DB::transaction(function () use (
            $request,
            $validated,
            $employeeId

        ) {

            if ($request->file('document_file')) {
                $file = $request->file('document_file');
                $directory = public_path('uploads/leave-documents');

                if (! is_dir($directory)) {
                    mkdir($directory, 0755, true);
                }

                $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $file->move($directory, $filename);

                $validated['document_file'] = 'uploads/leave-documents/' . $filename;
            }

            $leave = LeaveRequest::create([
                ...$validated,
                'employee_id' => $employeeId,
                'status' => 'pending',
                'current_step' => 1,
            ]);

            $hrRole = Role::where('name', 'hr')->first();
            $ownerRole = Role::where('name', 'owner')->first();

            leaveApproval::create([
                'leave_request_id' => $leave->id,
                'approval_order' => 1,
                'role_id' => $hrRole->id,
                'status' => 'pending',
            ]);

            leaveApproval::create([
                'leave_request_id' => $leave->id,
                'approval_order' => 2,
                'role_id' => $ownerRole->id,
                'status' => 'pending',
            ]);
        });

        return redirect()
            ->route('leave-request.index')
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

        $leaveStats = $this->leaveStatisticsPayload($employees, $leaveRequest);

        return view('leave.edit', compact('employees', 'leaveRequest', 'types', 'leaveStats'));
    }

    public function update(Request $request, LeaveRequest $leaveRequest)
    {
        $role = auth()->user()->role->name;

        if ($role === 'employee') {
            abort(403, 'Anda tidak dapat mengakses halaman ini.');
        }

        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'leave_id' => 'required|exists:leave_types,id',
            'document_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $leaveType = LeaveType::findOrFail($request->leave_id);

        if ($leaveType->document && ! $request->hasFile('document_file') && ! $leaveRequest->document_file) {
            return back()
                ->withInput()
                ->withErrors(['document_file' => 'Jenis cuti ini wajib melampirkan dokumen.']);
        }

        $start = \Carbon\Carbon::parse($request->start_date);
        $end = \Carbon\Carbon::parse($request->end_date);

        $daysRequested = $start->diffInDays($end) + 1;

        if (
            ! AttendancePolicy::isSuddenLeaveType($leaveType)
            && AttendancePolicy::scheduleOverlapQuery($request->employee_id, $start, $end)->exists()
        ) {
            return back()
                ->withInput()
                ->with('error', 'Cuti tidak bisa diajukan karena jadwal pada tanggal tersebut sudah digenerate. Gunakan jenis cuti dadakan jika memang mendadak.');
        }

        // ===============================
        // 1️⃣ VALIDASI MAX PER PENGAJUAN
        // ===============================

        if ($leaveType->max_days && $daysRequested > $leaveType->max_days) {
            return back()->with(
                'error',
                'Maksimal pengajuan ' . $leaveType->max_days . ' hari.'
            );
        }

        // ===============================
        // 2️⃣ VALIDASI LIMIT PERIODE
        // ===============================

        if ($leaveType->limit_days && $leaveType->limit_type) {

            $query = LeaveRequest::where('employee_id', $request->employee_id)
                ->where('leave_id', $leaveType->id)
                ->whereIn('status', AttendancePolicy::APPROVED_LEAVE_STATUSES)
                ->where('id', '!=', $leaveRequest->id); // 🔥 EXCLUDE DATA LAMA

            if ($leaveType->limit_type === 'yearly') {
                $query->whereYear('start_date', $start->year);
            }

            if ($leaveType->limit_type === 'monthly') {
                $query->whereYear('start_date', $start->year)
                    ->whereMonth('start_date', $start->month);
            }

            $usedDays = $query->sum(
                \DB::raw('DATEDIFF(end_date, start_date) + 1')
            );

            if (($usedDays + $daysRequested) > $leaveType->limit_days) {

                $remaining = $leaveType->limit_days - $usedDays;

                return back()->with(
                    'error',
                    'Sisa cuti hanya ' . $remaining . ' hari.'
                );
            }
        }

        // ===============================
        // 3️⃣ HANDLE FILE
        // ===============================

        if ($request->file('document_file')) {
            $oldDocument = $leaveRequest->document_file
                ? public_path($leaveRequest->document_file)
                : null;

            if ($oldDocument && file_exists($oldDocument)) {
                unlink($oldDocument);
            }

            $file = $request->file('document_file');
            $directory = public_path('uploads/leave-documents');

            if (! is_dir($directory)) {
                mkdir($directory, 0755, true);
            }

            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $file->move($directory, $filename);

            $validated['document_file'] = 'uploads/leave-documents/' . $filename;
        }

        // ===============================
        // 4️⃣ UPDATE DATA
        // ===============================

        $leaveRequest->update($validated);

        return redirect()
            ->route('leave-request.index')
            ->with('success', 'Data telah Diubah');
    }

    public function destroy(LeaveRequest $leaveRequest)
    {
        $role = auth()->user()->role->name;

        if ($role !== 'owner') {
            abort(403, 'Anda tidak memiliki akses');
        }

        // Pending hanya boleh dihapus saat step owner
        if (
            $leaveRequest->status === 'pending' &&
            $leaveRequest->current_step != 2
        ) {
            abort(403, 'Data tidak dapat dihapus.');
        }

        // Selain pending dan rejected tidak boleh dihapus
        if (!in_array($leaveRequest->status, ['pending', 'rejected'])) {
            abort(403, 'Data tidak dapat dihapus.');
        }

        $leaveRequest->delete();

        return redirect()
            ->route('leave-request.index')
            ->with('success', 'Data telah dihapus.');
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
            ->with('success', 'Cuti telah direject.');
    }
}
