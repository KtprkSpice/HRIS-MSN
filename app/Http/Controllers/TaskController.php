<?php

namespace App\Http\Controllers;

use App\Models\Presence;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        $presences = Presence::find('id');
        $tasks = Task::all();
        return view('tasks.index', compact('tasks', 'presences'));
    }

    public function create()
    {
        return view('tasks.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable',
            'start_time' => 'required|date',
            'end_time' => 'required|date',
        ]);

        $request->merge([
            'status' => 'pending'
        ]);

        Task::create($request->all());

        return redirect()->route('task.index')->with('success', "Tugas Telah Dibuat");
    }

    public function edit(Task $task) {
        return view('tasks.edit', compact('task'));
    }

    public function update(Request $request, Task $task) {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable',
            'start_time' => 'required|date',
            'end_time' => 'required|date',
        ]);

        $task->update($request->all());
        return redirect()->route('task.index')->with('success', 'Data Berhasil Diubah');
    }

    public function destroy(Task $task) {
        $task->delete();

        return redirect()->route('task.index')->with('success', "Data $task->name Telah Dihapus");
    }

    public function done($id) {
        $tasks = Task::find($id);
        $taskName = $tasks->name;
        $tasks->update([
            'status' => 'done',
        ]);
        
        return redirect()->route('task.index')->with('success', "Tugas $taskName telah diupdate menjadi Done");
    }

    public function pending($id) {
        $tasks = Task::find($id);
        $taskName = $tasks->name;
        $tasks->update([
            'status' => 'pending'
        ]);

        return redirect()->route('task.index')->with('success', "Tugas $taskName telah diupdate menjadi Pending");
    }

    public function onduty($id) {
        $tasks = Task::find($id);
        $taskName = $tasks->name;
        $tasks->update([
            'status' => 'on duty'
        ]);

        return redirect()->route('task.index')->with('success', "Tugas $taskName telah diupdate menjadi On Duty");
    }

}
