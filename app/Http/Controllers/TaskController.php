<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::all();
        return view('tasks.index', compact('tasks'));
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

    public function destroy(Task $task) {
        $task->delete();

        return redirect()->route('task.index')->with('success', "Data $task->name Telah Dihapus");
    }
}
