<?php

namespace App\Http\Controllers;

use App\Exports\TaskAssignmentExport;
use App\Imports\TaskScheduleImport;
use App\Models\Employee;
use App\Models\Schedule;
use App\Models\Shift;
use App\Models\Task;
use App\Models\Tasklocation;
use App\Models\TaskShift;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

use function Symfony\Component\Clock\now;

class TaskController extends Controller
{
    private function taskSchedulePeriod(Task $task, ?string $weekStart = null): array
    {
        $taskStart = Carbon::parse($task->start_time)->startOfDay();
        $taskEnd = Carbon::parse($task->end_time)->startOfDay();
        $selectedWeekStart = $weekStart
            ? Carbon::parse($weekStart)->startOfWeek()
            : Carbon::now()->startOfWeek();

        $periodStart = $selectedWeekStart->max($taskStart);

        if ($periodStart->gt($taskEnd)) {
            $periodStart = $taskEnd->copy();
        }

        $periodEnd = $periodStart->copy()->addDays(6)->min($taskEnd);

        return [$periodStart, $periodEnd];
    }

    private function availableEmployeeQuery(?Task $task = null): Builder
    {
        return Employee::where('status', 'active')
            ->whereHas('user.role', function ($q) {
                $q->where('name', 'employee');
            })
            ->whereDoesntHave('tasks', function ($q) use ($task) {
                $q->where('tasks.status', 'on duty')
                    ->whereNull('employees_tasks.deleted_at')
                    ->when($task, function ($q) use ($task) {
                        $q->where('tasks.id', '!=', $task->id);
                    });
            });
    }

    public function index()
    {
        $user = auth()->user();
        $roles = auth()->user()->role->name;
        if ($roles === 'employee') {
            abort(403, 'Anda tidak memiliki akses');
        }

        $tasks = Task::all();

        return view('tasks.index', compact('tasks'));
    }

    public function show(Request $request, Task $task)
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

        $validated = $request->validate([
            'week_start' => 'nullable|date',
        ]);

        [$weekStart, $weekEnd] = $this->taskSchedulePeriod($task, $validated['week_start'] ?? null);
        $weekDates = collect();

        for ($date = $weekStart->copy(); $date->lte($weekEnd); $date->addDay()) {
            $weekDates->push($date->copy());
        }

        $employees = $task->employees()->with(['division', 'position'])->where('status', 'active')->get();
        $locations = Tasklocation::where('task_id', $task->id)->first();
        $schedules = Schedule::where('task_id', $task->id)
            ->whereBetween('date', [$weekStart->toDateString(), $weekEnd->toDateString()])
            ->with(['employee', 'shift'])
            ->get()
            ->groupBy(fn($schedule) => $schedule->employee_id . '_' . Carbon::parse($schedule->date)->toDateString());
        $shifts = $task->Shift()->orderBy('start_time')->get();

        return view('tasks.show', compact('task', 'employees', 'schedules', 'locations', 'shifts', 'weekStart', 'weekEnd', 'weekDates'));
    }

    public function export(Request $request, Task $task)
    {
        $roles = auth()->user()->role->name;

        if (! in_array($roles, ['hr', 'owner'])) {
            abort(403);
        }

        $validated = $request->validate([
            'week_start' => 'nullable|date',
        ]);

        [$weekStart, $weekEnd] = $this->taskSchedulePeriod($task, $validated['week_start'] ?? null);

        $task->load([
            'employees.division',
            'employees.position',
            'location',
            'Shift',
        ]);

        $safeTaskName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $task->name);
        $fileName = 'Template_Jadwal_' . $safeTaskName . '_' . $weekStart->format('d-m-Y') . '_sd_' . $weekEnd->format('d-m-Y') . '.xlsx';

        return Excel::download(new TaskAssignmentExport($task, $weekStart, $weekEnd), $fileName);
    }

    public function importSchedule(Request $request, Task $task)
    {
        $roles = auth()->user()->role->name;

        if (! in_array($roles, ['hr', 'owner'])) {
            abort(403);
        }

        $validated = $request->validate([
            'schedule_file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        $task->load([
            'employees',
            'Shift',
        ]);

        Excel::import(new TaskScheduleImport($task), $validated['schedule_file']);

        return redirect()
            ->route('task.show', $task->id)
            ->with('success', 'Jadwal kerja berhasil di-import dari Excel');
    }

    public function create()
    {
        $roles = auth()->user()->role->name;

        if ($roles === 'employee') {
            abort('403');
        } else {

            $employees = $this->availableEmployeeQuery()->get();
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

                // Shift Create
                // Shift Name
                'shift_name' => 'required|array|min:1',
                'shift_name.*' => 'required|string|max:100',

                // Shift Start
                'shift_start' => 'required|array|min:1',
                'shift_start.*' => 'required|date_format:H:i',

                // Shift End
                'shift_end' => 'required|array|min:1',
                'shift_end.*' => 'required|date_format:H:i',

                // Shift Late
                'shift_late_tolerance' => 'required|array|min:1',
                'shift_late_tolerance.*' => 'required|integer|min:0',
            ]);

            DB::transaction(function () use ($request) {
                $task = Task::create([
                    'name' => $request->name,
                    'start_time' => $request->start_time,
                    'end_time' => $request->end_time,
                    'description' => $request->description,
                    'status' => 'pending',
                ]);

                // Create Shift
                foreach ($request->shift_name as $index => $name) {
                    $task->Shift()->create([
                        'name' => $request->shift_name[$index],
                        'start_time' => $request->shift_start[$index],
                        'end_time' => $request->shift_end[$index],
                        'late_tolerance_minutes' => $request->shift_late_tolerance[$index],
                    ]);
                }

                Tasklocation::create([
                    'task_id' => $task->id,
                    'name' => 'Lokasi Utama',
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                    'radius' => $request->radius,
                    'is_active' => true
                ]);

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

            $employees = $this->availableEmployeeQuery($task)->get();
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

                // Shift Create
                // Shift ID
                'shift_ids' => 'nullable|array',
                'shift_ids.*' => 'nullable|integer',
                // Shift Name
                'shift_name' => 'required|array|min:1',
                'shift_name.*' => 'required|string|max:100',

                // Shift Start
                'shift_start' => 'required|array|min:1',
                'shift_start.*' => 'required|date_format:H:i',

                // Shift End
                'shift_end' => 'required|array|min:1',
                'shift_end.*' => 'required|date_format:H:i',

                // Shift Late
                'shift_late_tolerance' => 'required|array|min:1',
                'shift_late_tolerance.*' => 'required|integer|min:0',
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
                 * 2. UPDATE / CREATE SHIFT
                 * ======================= */

                $shiftIds = $request->shift_ids ?? [];
                $processedShiftIds = [];

                foreach ($request->shift_name as $index => $shiftName) {

                    $shiftId = $shiftIds[$index] ?? null;

                    $shift = Shift::updateOrCreate(
                        [
                            'id' => $shiftId,
                            'task_id' => $task->id,
                        ],
                        [
                            'name' => $shiftName,
                            'start_time' => $request->shift_start[$index],
                            'end_time' => $request->shift_end[$index],
                            'late_tolerance_minutes' => $request->shift_late_tolerance[$index],
                        ]
                    );

                    $processedShiftIds[] = $shift->id;
                }

                /* =======================
                 * 3. CARI SHIFT YANG DIHAPUS
                 * ======================= */

                $shiftsToDelete = $task->Shift()
                    ->whereNotIn('id', $processedShiftIds)
                    ->get();

                /* =======================
                 * 4. CEK SEBELUM DELETE
                 * ======================= */

                foreach ($shiftsToDelete as $shiftToDelete) {

                    // GANTI dengan tabel yang benar-benar
                    // menggunakan TaskShift tersebut.
                    $isUsed = DB::table('presences')
                        ->where('shift_id', $shiftToDelete->id)
                        ->exists()
                        || DB::table('schedules')
                        ->where('shift_id', $shiftToDelete->id)
                        ->exists();

                    if ($isUsed) {
                        throw new \Exception(
                            "Jadwal '{$shiftToDelete->name}' tidak dapat dihapus karena sudah digunakan."
                        );
                    }

                    $shiftToDelete->delete();
                }

                /* =======================
                 * 5. UPDATE LOCATION
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
                 * 6. SYNC EMPLOYEES
                 * ======================= */

                $selectedEmployeeIds = $request->selected_employee ?? [];

                $activeEmployeeIds = $task->employees()
                    ->pluck('employees.id')
                    ->toArray();

                $trashedEmployeeIds = $task->employeesWithTrashed()
                    ->wherePivotNotNull('deleted_at')
                    ->pluck('employees.id')
                    ->toArray();

                // REMOVE
                $toDetach = array_diff(
                    $activeEmployeeIds,
                    $selectedEmployeeIds
                );

                if (! empty($toDetach)) {
                    DB::table('employees_tasks')
                        ->where('task_id', $task->id)
                        ->whereIn('employee_id', $toDetach)
                        ->update([
                            'deleted_at' => now(),
                        ]);
                }

                // RESTORE
                $toRestore = array_intersect(
                    $trashedEmployeeIds,
                    $selectedEmployeeIds
                );

                foreach ($toRestore as $employeeId) {
                    DB::table('employees_tasks')
                        ->where('task_id', $task->id)
                        ->where('employee_id', $employeeId)
                        ->update([
                            'deleted_at' => null,
                        ]);
                }

                // ATTACH BARU
                $toAttach = array_diff(
                    $selectedEmployeeIds,
                    array_merge(
                        $activeEmployeeIds,
                        $trashedEmployeeIds
                    )
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
