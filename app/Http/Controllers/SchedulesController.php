<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeOffDay;
use App\Models\Schedule;
use App\Models\Shift;
use App\Models\Task;
use App\Models\TaskShiftRule;
use App\Support\AttendancePolicy;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SchedulesController extends Controller
{
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
            $employees = Employee::select('id', 'fullname')->orderBy('fullname')->get();
            $shifts = Shift::select('name', 'id')->orderBy('id')->get();
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

            if (AttendancePolicy::hasApprovedLeaveOnDate((int) $validated['employee_id'], $validated['date'])) {
                return back()
                    ->withInput()
                    ->with('error', 'Karyawan sedang cuti approved pada tanggal tersebut.');
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
        } else {
            $employees = Employee::select('fullname', 'id')->orderBy('fullname')->get();
            $shifts = Shift::select('id', 'name')->orderBy('id')->get();
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
        } else {
            $validated = $request->validate([
                'employee_id' => 'required|exists:employees,id',
                'shift_id' => 'required|exists:shifts,id',
                'task_id' => 'required|exists:tasks,id',
                'date' => 'required|date',
            ]);

            if (AttendancePolicy::hasApprovedLeaveOnDate((int) $validated['employee_id'], $validated['date'])) {
                return back()
                    ->withInput()
                    ->with('error', 'Karyawan sedang cuti approved pada tanggal tersebut.');
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
        } else {
            DB::transaction(function () {

                $weekStart = Carbon::now()->startOfWeek();
                $weekEnd = Carbon::now()->endOfWeek();

                // 1. Ambil task aktif
                $tasks = Task::whereIn('status', ['on duty', 'pending'])->get();

                if ($tasks->isEmpty()) {
                    throw new \Exception('Tidak ada task aktif.');
                }

                foreach ($tasks as $task) {

                    // 2. Ambil employee task
                    $employees = Employee::whereHas('tasks', function ($q) use ($task) {
                        $q->where('task_id', $task->id);
                    })->get();

                    if ($employees->isEmpty()) {
                        continue;
                    }

                    // 3. Ambil / buat shift rule
                    $rules = TaskShiftRule::where('task_id', $task->id)
                        ->get()
                        ->keyBy('shift_id');

                    if ($rules->isEmpty()) {
                        foreach ([1, 2, 3] as $shiftId) {
                            TaskShiftRule::create([
                                'task_id' => $task->id,
                                'shift_id' => $shiftId,
                                'min_employee' => 1,
                            ]);
                        }

                        $rules = TaskShiftRule::where('task_id', $task->id)
                            ->get()
                            ->keyBy('shift_id');
                    }

                    $shiftCount = $rules->count();

                    // 4. Pastikan 1 hari libur / minggu
                    foreach ($employees as $employee) {
                        EmployeeOffDay::firstOrCreate(
                            [
                                'employee_id' => $employee->id,
                                'task_id' => $task->id,
                            ],
                            [
                                'day_of_week' => rand(0, 6),
                            ]
                        );
                    }

                    // 5. Loop per hari
                    for ($date = $weekStart->copy(); $date <= $weekEnd; $date->addDay()) {

                        $dayOfWeek = $date->dayOfWeek;

                        $offEmployeeIds = EmployeeOffDay::where('task_id', $task->id)
                            ->where('day_of_week', $dayOfWeek)
                            ->pluck('employee_id');

                        $leaveEmployeeIds = $employees
                            ->filter(fn ($employee) => AttendancePolicy::hasApprovedLeaveOnDate($employee->id, $date))
                            ->pluck('id');

                        // Jadwal yang sudah ada tidak dibuat ulang. Hanya slot yang belum ada
                        // untuk task dan tanggal ini yang akan diproses.
                        $scheduledEmployeeIds = Schedule::where('task_id', $task->id)
                            ->whereDate('date', $date)
                            ->pluck('employee_id');

                        $availableEmployees = $employees
                            ->whereNotIn('id', $offEmployeeIds)
                            ->whereNotIn('id', $leaveEmployeeIds)
                            ->whereNotIn('id', $scheduledEmployeeIds)
                            ->shuffle()
                            ->values();

                        if ($availableEmployees->isEmpty()) {
                            continue;
                        }

                        $usedEmployeeIds = collect();

                        // 6. Hitung distribusi ideal
                        $idealPerShift = max(
                            1,
                            floor($availableEmployees->count() / $shiftCount)
                        );

                        // 7. Generate per shift (FAIR)
                        foreach ($rules as $shiftId => $rule) {

                            $needed = max($rule->min_employee, $idealPerShift);

                            $candidates = $availableEmployees
                                ->whereNotIn('id', $usedEmployeeIds)
                                ->take($needed);

                            foreach ($candidates as $employee) {
                                Schedule::firstOrCreate(
                                    [
                                        'employee_id' => $employee->id,
                                        'task_id' => $task->id,
                                        'date' => $date->toDateString(),
                                    ],
                                    [
                                        'shift_id' => $shiftId,
                                        'source' => 'system',
                                    ]
                                );

                                $usedEmployeeIds->push($employee->id);
                            }
                        }
                    }
                }
            });
        }

        return redirect()->back()->with('success', 'Jadwal 1 minggu berhasil digenerate secara profesional');
    }
}
