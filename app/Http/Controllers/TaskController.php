<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Schedule;
use App\Models\Task;
use App\Models\Tasklocation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use function Symfony\Component\Clock\now;

class TaskController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $roles = auth()->user()->role->name;
        if ($roles === 'owner') {

            $tasks = Task::all();
        } elseif ($roles === 'employee') {
            $tasks = $user->employee->tasks;
        }

        return view('tasks.index', compact('tasks'));
    }

    public function show(Task $task)
    {

        $user = auth()->user();
        $roles = auth()->user()->role->name;

        if ($roles === 'employee') {
            $hasAccsess = $task->employees()
                ->where('employee_id', $user->employee->id)
                ->exists();

            if (! $hasAccsess) {
                abort(403, 'anda tidak memiliki akses ke task ini');
            }
        }

        $today = Carbon::now()->today();
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();
        $employees = $task->employees()->with('division')->get();
        $locations = Tasklocation::where('task_id', $task->id)->first();
        $schedules = Schedule::where('task_id', $task->id)
            ->where('date', [$today])
            ->with(['employee', 'shift'])
            ->get()
            ->groupBy('shift_id');

        return view('tasks.show', compact('task', 'employees', 'schedules', 'locations'));
    }

    public function create()
    {
        $roles = auth()->user()->role->name;

        if ($roles === 'employee') {
            abort('403');
        } else {

            $employees = Employee::all();
        }

        return view('tasks.create', compact('employees'));
    }

    public function store(Request $request)
    {

        $roles = auth()->user()->role->name;

        if ($roles === 'employee') {
            abort(403);
        } else {
            $request->validate([
                'name' => 'required|string',
                'start_time' => 'required|date',
                'end_time' => 'required|date',
                'description' => 'required|string',
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'radius' => 'required|integer|min:10',
                'selected_employee' => 'array',
            ]);

            DB::transaction(function () use ($request) {
                $task = Task::create(['name' => $request->name,
                    'start_time' => $request->start_time,
                    'end_time' => $request->end_time,
                    'description' => $request->description,
                    'status' => 'pending',
                ]);

                Tasklocation::create(['task_id' => $task->id,
                    'name' => 'Lokasi Utama',
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                    'radius' => $request->radius,
                    'is_active' => true]);

                foreach ($request->selected_employee ?? [] as $employeeId) {
                    $task->employees()->attach($employeeId, [
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            });
        }

        return redirect()->route('task.index')->with('success', 'Task, lokasi, dan jadwal berhasil dibuat');
    }

    public function edit(Task $task)
    {

        $user = auth()->user();
        $roles = auth()->user()->role->name;

        if ($roles === 'employee') {
            abort(403);
        } else {

            $employees = Employee::all();
            $locations = Tasklocation::where('task_id', $task->id)->first();
        }

        return view('tasks.edit', compact('task', 'employees', 'locations'));
    }

    public function update(Request $request, Task $task)
    {

        $roles = auth()->user()->role->name;

        if ($roles === 'employee') {
            abort(403);
        } else {
            $request->validate([
                'name' => 'required|string',
                'start_time' => 'required|date',
                'end_time' => 'required|date',
                'description' => 'required|string',
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'radius' => 'required|integer|min:10',
                'selected_employee' => 'array',
            ]);

            DB::transaction(function () use ($request, $task) {

                /* =======================
                 * 1. UPDATE TASK
                 * ======================= */
                $task->update([
                    'name' => $request->name,
                    'start_time' => $request->start_time,
                    'end_time' => $request->end_time,
                    'description' => $request->description,
                ]);

                /* =======================
                 * 2. UPDATE / CREATE LOCATION
                 * ======================= */
                Tasklocation::updateOrCreate(
                    [
                        'task_id' => $task->id,
                    ],
                    [
                        'latitude' => $request->latitude,
                        'longitude' => $request->longitude,
                        'radius' => $request->radius,
                        'is_active' => true,
                    ]
                );

                /* =======================
                 * 3. SYNC EMPLOYEES (SOFT DELETE AWARE)
                 * ======================= */

                $selectedEmployeeIds = $request->selected_employee ?? [];

                // employee AKTIF sekarang
                $activeEmployeeIds = $task->employees()
                    ->pluck('employees.id')
                    ->toArray();

                // employee SOFT DELETED
                $trashedEmployeeIds = $task->employeesWithTrashed()
                    ->wherePivotNotNull('deleted_at')
                    ->pluck('employees.id')
                    ->toArray();

                /* -------- REMOVE (soft delete) -------- */
                $toDetach = array_diff($activeEmployeeIds, $selectedEmployeeIds);

                if (! empty($toDetach)) {
                    DB::table('employees_tasks')
                        ->where('task_id', $task->id)
                        ->whereIn('employee_id', $toDetach)
                        ->update(['deleted_at' => now()]);
                }

                /* -------- RESTORE -------- */
                $toRestore = array_intersect($trashedEmployeeIds, $selectedEmployeeIds);

                foreach ($toRestore as $employeeId) {
                    DB::table('employees_tasks')
                        ->where('task_id', $task->id)
                        ->where('employee_id', $employeeId)
                        ->update(['deleted_at' => null]);
                }

                /* -------- ATTACH BARU -------- */
                $toAttach = array_diff(
                    $selectedEmployeeIds,
                    array_merge($activeEmployeeIds, $trashedEmployeeIds)
                );

                foreach ($toAttach as $employeeId) {
                    DB::table('employees_tasks')->insert([
                        'task_id' => $task->id,
                        'employee_id' => $employeeId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            });
        }

        return redirect()
            ->route('task.index')
            ->with('success', 'Task berhasil diperbarui');
    }

    public function destroy(Task $task)
    {
        $user = auth()->user();
        $roles = auth()->user()->role->name;

        if (in_array($roles, ['hr', 'owner'])) {
            $task->delete();

        } else {
            abort(403);
        }

        return redirect()->route('task.index')->with('success', "Data $task->name Telah Dihapus");
    }

    public function done($id)
    {

        $user = auth()->user();
        $roles = auth()->user()->role->name;

        if (in_array($roles, ['hr', 'owner'])) {
            $tasks = Task::find($id);
            $taskName = $tasks->name;
            $tasks->update([
                'status' => 'done',
            ]);
        } else {
            abort(403);
        }

        return redirect()->route('task.index')->with('success', "Tugas $taskName telah diupdate menjadi Done");
    }

    public function pending($id)
    {
        $user = auth()->user();
        $roles = auth()->user()->role->name;

        if (in_array($roles, ['hr', 'owner'])) {
            $tasks = Task::find($id);
            $taskName = $tasks->name;
            $tasks->update([
                'status' => 'pending',
            ]);
        } else {
            abort(403);
        }

        return redirect()->route('task.index')->with('success', "Tugas $taskName telah diupdate menjadi Pending");
    }

    public function onduty($id)
    {
        $user = auth()->user();
        $roles = auth()->user()->role->name;

        if (in_array($roles, ['hr', 'owner'])) {
            $tasks = Task::find($id);
            $taskName = $tasks->name;
            $tasks->update([
                'status' => 'on duty',
            ]);
        } else {
            abort(403);
        }

        return redirect()->route('task.index')->with('success', "Tugas $taskName telah diupdate menjadi On Duty");
    }
}
