<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Presence;
use App\Models\Schedule;
use App\Models\Task;
use App\Models\Tasklocation;
use Illuminate\Http\Request;

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
        $employees = $task->employees()->with('division')->get();
        $schedules = Schedule::where('task_id', $task->id)->with(['employee', 'shift'])->get()->groupBy('shift_id');
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

            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'required|integer|min:10',
        ]);

        $task = Task::create([
            'name' => $request->name,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'description' => $request->description,
        ]);

        Tasklocation::create([
            'task_id' => $task->id,
            'name' => 'Lokasi Utama',
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'radius' => $request->radius,
            'is_active' => true,
        ]);

        return redirect()->route('task.index')
            ->with('success', 'Task & lokasi berhasil disimpan');
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
            // Locations
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'required|integer|min:10',
        ]);

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

        $selectedEmployeeId = $request->selected_employee ?? [];

        $currentEmployeeId = $task->employees()->pluck('employees.id')->toArray();

        $toRemove = array_diff($currentEmployeeId, $selectedEmployeeId);

        if (! empty($toRemove)) {
            $task->employees()->wherePivotIn('employee_id', $toRemove)->updateExistingPivot($toRemove, ['deleted_at' => now()]);
        }

        foreach ($selectedEmployeeId as $employeeId) {
            $task->employees()->syncWithoutDetaching([
                $employeeId => ['deleted_at' => null],
            ]);
        }

        return redirect()->route('task.index')->with('success', 'Data Berhasil Diubah');
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
