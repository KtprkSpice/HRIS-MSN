<?php

namespace App\Http\Controllers;

use App\Actions\AssignEmployeeOffDayService;
use App\Actions\GenerateDailySchedule;
use App\Actions\GenerateTaskShiftRule;
use App\Actions\GenerateWeeklyShiftAssignment;
use App\Models\Employee;
use App\Models\Presence;
use App\Models\Schedule;
use App\Models\Task;
use App\Models\Tasklocation;
use App\Models\WeeklyShiftAssignment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use function Symfony\Component\Clock\now;

class TaskController extends Controller
{
    public function index()
    {
        $presences = Presence::find('id');
        $tasks = Task::all();

        return view('tasks.index', compact('tasks', 'presences'));
    }

    public function show(Task $task)
    {
        $today = Carbon::now()->today();
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();
        $employees = $task->employees()->with('division')->get();
        $schedules = Schedule::where('task_id', $task->id)->where('date', [$today])->with(['employee', 'shift'])->get()->groupBy('shift_id');
        $locations = Tasklocation::where('task_id', $task->id)->first();

        return view('tasks.show', compact('task', 'employees', 'schedules', 'locations'));
    }

    public function create()
    {
        $employees = Employee::all();

        return view('tasks.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'start_time' => 'required|date',
            'end_time' => 'required|date',
            'description' => 'required|string',

            // Locations
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'required|integer|min:10',

            // Employees
            'selected_employee' => 'array',
        ]);

        DB::transaction(function () use ($request) {

            // =========================
            // 1️⃣ CREATE TASK
            // =========================
            $task = Task::create([
                'name' => $request->name,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'description' => $request->description,
                'status' => 'on duty',
            ]);

            // =========================
            // 2️⃣ CREATE LOCATION
            // =========================
            Tasklocation::create([
                'task_id' => $task->id,
                'name' => 'Lokasi Utama',
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'radius' => $request->radius,
                'is_active' => true,
            ]);

            // =========================
            // 3️⃣ HANDLE EMPLOYEE ASSIGNMENT
            // =========================
            $selectedEmployeeIds = $request->selected_employee ?? [];

            if (empty($selectedEmployeeIds)) {
                return;
            }

            $existingEmployeeIds = $task->employees()
                ->withPivot('deleted_at')
                ->pluck('employees.id')
                ->toArray();

            $newEmployeeIds = array_diff($selectedEmployeeIds, $existingEmployeeIds);

            foreach ($selectedEmployeeIds as $employeeId) {
                $task->employees()->syncWithoutDetaching([
                    $employeeId => ['deleted_at' => null],
                ]);
            }

            // =========================
            // 4️⃣ AUTO ASSIGN OFF DAY
            // =========================
            foreach ($newEmployeeIds as $employeeId) {
                $employee = Employee::findOrFail($employeeId);

                app(AssignEmployeeOffDayService::class)
                    ->assign($employee, $task);
            }

            // =========================
            // 5️⃣ AUTO CREATE / UPDATE TASK SHIFT RULE
            // =========================
            app(GenerateTaskShiftRule::class)
                ->handle($task);

            // =========================
            // 6️⃣ GENERATE WEEKLY + DAILY SCHEDULE
            // =========================
            $weekStart = Carbon::now()->startOfWeek();

            app(GenerateWeeklyShiftAssignment::class)
                ->handle($task, $weekStart);

            app(GenerateDailySchedule::class)
                ->handle($task, $weekStart);
        });

        return redirect()
            ->route('task.index')
            ->with('success', 'Task, lokasi, dan jadwal berhasil dibuat');
    }

    public function edit(Task $task)
    {
        $employees = Employee::all();
        $locations = Tasklocation::where('task_id', $task->id)->first();

        return view('tasks.edit', compact('task', 'employees', 'locations'));
    }

    public function update(Request $request, Task $task)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable',
            'start_time' => 'required|date',
            'end_time' => 'required|date',

            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'required|integer|min:10',

            'selected_employee' => 'array',
        ]);

        DB::transaction(function () use ($request, $task) {

            // =========================
            // 1️⃣ UPDATE TASK
            // =========================
            $task->update([
                'name' => $request->name,
                'description' => $request->description,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
            ]);

            Tasklocation::where('task_id', $task->id)->update([
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'radius' => $request->radius,
            ]);

            // =========================
            // 2️⃣ HANDLE EMPLOYEE UPDATE
            // =========================
            $selectedEmployeeIds = $request->selected_employee ?? [];

            $currentEmployeeIds = $task->employees()
                ->pluck('employees.id')
                ->toArray();

            $toRemove = array_diff($currentEmployeeIds, $selectedEmployeeIds);

            if (! empty($toRemove)) {
                $task->employees()
                    ->wherePivotIn('employee_id', $toRemove)
                    ->updateExistingPivot($toRemove, ['deleted_at' => now()]);
            }

            foreach ($selectedEmployeeIds as $employeeId) {
                $task->employees()->syncWithoutDetaching([
                    $employeeId => ['deleted_at' => null],
                ]);
            }

            // =========================
            // 3️⃣ REGENERATE RULE + SCHEDULE
            // =========================
            app(GenerateTaskShiftRule::class)
                ->handle($task);

            $weekStart = Carbon::now()->startOfWeek();

            // Hapus weekly & daily lama (minggu ini)
            WeeklyShiftAssignment::where('task_id', $task->id)
                ->where('week_start_date', $weekStart)
                ->delete();

            Schedule::where('task_id', $task->id)
                ->whereBetween('date', [$weekStart, $weekStart->copy()->endOfWeek()])
                ->delete();

            app(GenerateWeeklyShiftAssignment::class)
                ->handle($task, $weekStart);

            app(GenerateDailySchedule::class)
                ->handle($task, $weekStart);
        });

        return redirect()
            ->route('task.index')
            ->with('success', 'Task dan jadwal berhasil diperbarui');
    }

    public function destroy(Task $task)
    {
        $task->delete();

        return redirect()->route('task.index')->with('success', "Data $task->name Telah Dihapus");
    }

    public function done($id)
    {
        $tasks = Task::find($id);
        $taskName = $tasks->name;
        $tasks->update([
            'status' => 'done',
        ]);

        return redirect()->route('task.index')->with('success', "Tugas $taskName telah diupdate menjadi Done");
    }

    public function pending($id)
    {
        $tasks = Task::find($id);
        $taskName = $tasks->name;
        $tasks->update([
            'status' => 'pending',
        ]);

        return redirect()->route('task.index')->with('success', "Tugas $taskName telah diupdate menjadi Pending");
    }

    public function onduty($id)
    {
        $tasks = Task::find($id);
        $taskName = $tasks->name;
        $tasks->update([
            'status' => 'on duty',
        ]);

        return redirect()->route('task.index')->with('success', "Tugas $taskName telah diupdate menjadi On Duty");
    }
}
