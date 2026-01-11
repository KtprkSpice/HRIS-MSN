<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Schedule;
use App\Models\Shift;
use App\Models\Task;
use Illuminate\Http\Request;

class SchedulesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $schedules = Schedule::all()->except('created_at', 'updated_at', 'deleted_at');

        return view('Schedules.index', compact('schedules'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $employees = Employee::select('id', 'fullname')->orderBy('fullname')->get();
        $shifts = Shift::select('name', 'id')->orderBy('id')->get();
        $tasks = Task::select('name', 'id')->where('status', 'pending')->orderBy('name')->get();

        return view('Schedules.create', compact('employees', 'shifts', 'tasks'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'shift_id' => 'required|exists:shifts,id',
            'task_id' => 'required|exists:tasks,id',
            'date' => 'required|date',
        ]);

        $validated['source'] = 'manual';

        Schedule::create($validated);

        return redirect()->route('schedule.index')->with('success', 'Jadwal Dibuat secara manual');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(schedule $schedule)
    {
        $employees = Employee::select('fullname', 'id')->orderBy('fullname')->get();
        $shifts = Shift::select('id', 'name')->orderBy('id')->get();
        $tasks = Task::select('id', 'name')->where('status', 'pending')->orderBy('name')->get();

        return view('Schedules.edit', compact('employees', 'shifts', 'tasks', 'schedule'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, schedule $schedule)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'shift_id' => 'required|exists:shifts,id',
            'task_id' => 'required|exists:tasks,id',
            'date' => 'required|date',
        ]);

        $validated['source'] = 'manual';

        $schedule->update($validated);

        return redirect()->route('schedule.index')->with('success', 'Jadwal telah diupdate secara manual');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(schedule $schedule)
    {
        $schedule->delete();

        return redirect()->route('schedule.index')->with('success', 'Jadwal Telah DiHapus');
    }
}
