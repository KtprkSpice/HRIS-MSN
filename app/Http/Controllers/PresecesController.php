<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Presence;
use App\Models\Task;
use Illuminate\Http\Request;

class PresecesController extends Controller
{
    public function index()
    {
        $presences = Presence::all();

        return view('presences.index', compact('presences'));
    }

    public function create()
    {
        $presences = Presence::all();
        $employees = Employee::all();

        return view('presences.create', compact('presences', 'employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required',
            'date' => 'required|date',
            'check_in' => 'required|date',
            'check_out' => 'required|date',
        ]);

        Presence::create($request->all());

        return redirect()->route('presence.index')->with('success', 'Data Presensi Telah Dibuat');
    }

    public function edit(Presence $presence)
    {
        $employees = Employee::all();

        return view('presences.edit', compact('employees', 'presence'));
    }

    public function update(Presence $presence, Request $request)
    {
        $request->validate([
            'employee_id' => 'required',
            'date' => 'required|date',
            'check_in' => 'required|date',
            'check_out' => 'required|date',
        ]);

        $presence->update($request->all());

        return redirect()->route('presence.index')->with('success', 'Data Berhasil Diubah');
    }

    public function destroy(Presence $presence)
    {
        $presence->delete();

        return redirect()->route('presence.index')->with('success', 'Data Telah dihapus');
    }

    public function scan($id)
    {
        $presences = Presence::find($id);
        $task = Task::find($id);

        return view('presences.scan', compact('presences', 'task'));
    }

    public function storeQr(Request $request)
    {
        $user = auth()->user();

        $task = Task::with('employees')->findOrFail($request->task->id);

        if (! $task->employees->contains($user->id)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda tidak terdaftar pada task ini.',
            ]);
            $already = Presence::where('task_id', $task->id)
                ->where('user_id', $user->id)
                ->whereDate('time_in', now()->toDateString())
                ->first();

            if ($already) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda sudah melakukan presensi hari ini.',
                ]);
            }

            // Simpan presensi dari QR
            Presence::create([
                'task_id' => $task->id,
                'user_id' => $user->id,
                'qr_data' => $request->qr_data,
                'time_in' => now(),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Presensi berhasil dicatat!',
            ]);
        }
    }
}
