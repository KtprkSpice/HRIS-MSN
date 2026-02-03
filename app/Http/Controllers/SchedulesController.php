<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeOffDay;
use App\Models\Schedule;
use App\Models\Shift;
use App\Models\Task;
use App\Models\TaskShiftRule;
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

    public function generate()
    {
        DB::transaction(function () {

            $tasks = Task::whereIn('status', ['on_duty', 'pending'])->get();

            if ($tasks->isEmpty()) {
                throw new \Exception('Tidak ada task aktif');
            }

            $weekStart = Carbon::now()->startOfWeek();
            $weekEnd = Carbon::now()->endOfWeek();

            foreach ($tasks as $task) {

                // ================================
                // 1. Ambil employee task
                // ================================
                $employees = Employee::whereHas('tasks', function ($q) use ($task) {
                    $q->where('task_id', $task->id);
                })->get();

                if ($employees->isEmpty()) {
                    continue;
                }

                // ================================
                // 2. Ambil shift rules
                // ================================
                $rules = TaskShiftRule::where('task_id', $task->id)
                    ->get()
                    ->keyBy('shift_id');

                if ($rules->isEmpty()) {
                    throw new \Exception("Task {$task->name} belum punya shift rule");
                }

                // ================================
                // 3. Pastikan libur 1 hari / minggu
                // ================================
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

                // ================================
                // 4. Loop per hari
                // ================================
                for ($date = $weekStart->copy(); $date <= $weekEnd; $date->addDay()) {

                    $dayOfWeek = $date->dayOfWeek;

                    // employee yang libur hari ini
                    $offEmployeeIds = EmployeeOffDay::where('task_id', $task->id)
                        ->where('day_of_week', $dayOfWeek)
                        ->pluck('employee_id');

                    // employee yang tersedia
                    $availableEmployees = $employees
                        ->whereNotIn('id', $offEmployeeIds)
                        ->shuffle()
                        ->values();

                    // simpan employee yg sudah dipakai hari ini
                    $usedEmployeeIds = collect();

                    // ================================
                    // 5. Generate per shift (PRIORITAS)
                    // ================================
                    foreach ([1, 2, 3] as $shiftId) {

                        if (! isset($rules[$shiftId])) {
                            continue;
                        }

                        $minEmployee = $rules[$shiftId]->min_employee;

                        $candidates = $availableEmployees
                            ->whereNotIn('id', $usedEmployeeIds)
                            ->values();

                        if ($candidates->isEmpty()) {
                            continue; // benar-benar tidak ada orang
                        }

                        // ambil sebanyak mungkin, max = min_employee
                        $candidates = $candidates->take($minEmployee);

                        foreach ($candidates as $employee) {
                            Schedule::create([
                                'employee_id' => $employee->id,
                                'task_id' => $task->id,
                                'shift_id' => $shiftId,
                                'date' => $date->toDateString(),
                                'source' => 'system',
                            ]);

                            $usedEmployeeIds->push($employee->id);
                        }
                    }
                }
            }
        });

        return redirect()->back()->with('success', 'Jadwal berhasil digenerate otomatis');
    }
}
