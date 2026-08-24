<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Schedule;
use App\Models\Shift;
use App\Models\Task;
use App\Support\AttendancePolicy;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SchedulesController extends Controller
{
    private function scheduleIsPast(Schedule $schedule): bool
    {
        return Carbon::parse($schedule->date)->startOfDay()->lt(now()->startOfDay());
    }

    private function validateScheduleAssignment(array $validated, ?Schedule $schedule = null): ?\Illuminate\Http\RedirectResponse
    {
        $task = Task::with('employees')->findOrFail($validated['task_id']);
        $shift = Shift::findOrFail($validated['shift_id']);

        if ((int) $shift->task_id !== (int) $task->id) {
            return back()
                ->withInput()
                ->with('error', 'Shift yang dipilih tidak terdaftar pada tugas tersebut.');
        }

        if (! $task->employees->contains('id', (int) $validated['employee_id'])) {
            return back()
                ->withInput()
                ->with('error', 'Karyawan belum terdaftar pada tugas tersebut.');
        }

        if (AttendancePolicy::hasApprovedLeaveOnDate((int) $validated['employee_id'], $validated['date'])) {
            return back()
                ->withInput()
                ->with('error', 'Karyawan sedang cuti approved pada tanggal tersebut.');
        }

        if (
            AttendancePolicy::scheduleOverlapQuery(
                (int) $validated['employee_id'],
                $validated['date'],
                $validated['date'],
                $schedule?->id
            )->exists()
        ) {
            return back()
                ->withInput()
                ->with('error', 'Karyawan sudah memiliki jadwal pada tanggal tersebut.');
        }

        return null;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        $role = auth()->user()->role->name;

        if ($role === 'employee') {
            $schedules = Schedule::where('employee_id', $user->employee->id)
                ->with(['task', 'shift'])
                ->get();
        } else {
            $schedules = Schedule::all()->except('created_at', 'updated_at', 'deleted_at');
        }

        return view('Schedules.index', compact('schedules'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $role = auth()->user()->role->name;

        if ($role === 'employee') {
            abort(403);
        } else {
            $employees = Employee::select('id', 'fullname')->where('status', 'active')->orderBy('fullname')->get();
            $shifts = Shift::with('task')->select('name', 'id', 'task_id')->orderBy('task_id')->orderBy('id')->get();
            $tasks = Task::select('name', 'id')->whereIn('status', ['pending', 'on duty'])->orderBy('name')->get();
        }

        return view('Schedules.create', compact('employees', 'shifts', 'tasks'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $role = auth()->user()->role->name;

        if ($role === 'employee') {
            abort(403);
        } else {
            $validated = $request->validate([
                'employee_id' => 'required|exists:employees,id',
                'shift_id' => 'required|exists:shifts,id',
                'task_id' => 'required|exists:tasks,id',
                'date' => 'required|date',
            ]);

            if ($errorResponse = $this->validateScheduleAssignment($validated)) {
                return $errorResponse;
            }

            $validated['source'] = 'manual';

            Schedule::create($validated);
        }

        return redirect()->route('schedule.index')->with('success', 'Jadwal Dibuat secara manual');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(schedule $schedule)
    {

        $role = auth()->user()->role->name;

        if ($role === 'employee') {
            abort(403);
        } elseif ($this->scheduleIsPast($schedule)) {
            abort(403, 'Jadwal yang tanggalnya sudah lewat tidak bisa diedit.');
        } else {
            $employees = Employee::select('fullname', 'id')->where('status', 'active')->orderBy('fullname')->get();
            $shifts = Shift::with('task')->select('id', 'name', 'task_id')->orderBy('task_id')->orderBy('id')->get();
            $tasks = Task::select('id', 'name')->whereIn('status', ['pending', 'on duty'])->orderBy('name')->get();
        }

        return view('Schedules.edit', compact('employees', 'shifts', 'tasks', 'schedule'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, schedule $schedule)
    {

        $role = auth()->user()->role->name;

        if ($role === 'employee') {
            abort(403);
        } elseif ($this->scheduleIsPast($schedule)) {
            abort(403, 'Jadwal yang tanggalnya sudah lewat tidak bisa diupdate.');
        } else {
            $validated = $request->validate([
                'employee_id' => 'required|exists:employees,id',
                'shift_id' => 'required|exists:shifts,id',
                'task_id' => 'required|exists:tasks,id',
                'date' => 'required|date',
            ]);

            if ($errorResponse = $this->validateScheduleAssignment($validated, $schedule)) {
                return $errorResponse;
            }

            $validated['source'] = 'swap';

            $schedule->update($validated);
        }

        return redirect()->route('schedule.index')->with('success', 'Jadwal telah diupdate secara manual');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(schedule $schedule)
    {
        $role = auth()->user()->role->name;

        if ($role === 'employee') {
            abort(403);
        } elseif ($this->scheduleIsPast($schedule)) {
            abort(403, 'Jadwal yang tanggalnya sudah lewat tidak bisa dihapus.');
        } else {
            $schedule->delete();
        }

        return redirect()->route('schedule.index')->with('success', 'Jadwal Telah DiHapus');
    }

    public function generate()
    {
        $role = auth()->user()->role->name;

        if ($role === 'employee') {
            abort(403);
        }

        return redirect()->back()->with('error', 'Generate jadwal otomatis sudah dinonaktifkan. Gunakan export Excel lalu import jadwal dari halaman tugas.');
    }
}
