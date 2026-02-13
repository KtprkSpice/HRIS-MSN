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
        $tasks = Task::select('name', 'id')->whereIn('status', ['pending', 'on duty'])->orderBy('name')->get();

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
        $tasks = Task::select('id', 'name')->whereIn('status', ['pending', 'on duty'])->orderBy('name')->get();

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

        $validated['source'] = 'swap';

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

            $weekStart = Carbon::now()->startOfWeek();
            $weekEnd = Carbon::now()->endOfWeek();

            // 0. Validasi jadwal existing
            if (
                Schedule::whereBetween('date', [
                    $weekStart->toDateString(),
                    $weekEnd->toDateString(),
                ])->exists()
            ) {
                throw new \Exception('Masih ada jadwal minggu ini. Hapus dulu sebelum generate ulang.');
            }

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

                    $availableEmployees = $employees
                        ->whereNotIn('id', $offEmployeeIds)
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

        return redirect()->back()->with('success', 'Jadwal 1 minggu berhasil digenerate secara profesional');
    }
}
