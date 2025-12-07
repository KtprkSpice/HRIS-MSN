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

    public function scan(Task $task)
    {
        $presences = Presence::find($task);

        return view('presences.scan', compact('presences', 'task'));
    }

    public function storeQr(Request $request)
    {
        \Log::info('DATA QR MASUK:', $request->all());

        try {

            // Validasi QR
            if (! $request->qr_data) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'QR tidak berisi data.',
                ], 400);
            }

            // Validasi Task
            if (! $request->task_id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Task ID tidak ditemukan.',
                ], 400);
            }

            // Ambil data user
            $employee = auth()->user()->employee;

            // Ambil task + pegawai yg terkait
            $task = Task::with('employees')->findOrFail($request->task_id);

            // Pastikan pegawai terdaftar pada task
            if (! $task->employees->contains($employee->id)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda tidak terdaftar pada task ini.',
                ]);
            }

            // Cek apakah sudah presensi hari ini
            $already = Presence::where('task_id', $task->id)
                ->where('employee_id', $employee->id)
                ->whereDate('date', today())
                ->first();

            if ($already) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda sudah melakukan presensi hari ini.',
                ]);
            }

            // Simpan presensi
            Presence::create([
                'employee_id' => $employee->id,
                'task_id' => $task->id,
                'date' => now()->toDateString(),
                'check_in' => now(),
                'check_out' => null,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Presensi berhasil dicatat!',
            ]);

        } catch (\Throwable $e) {

            \Log::error('ERROR PRESENSI: '.$e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Server error: '.$e->getMessage(),
            ], 500);
        }
    }
}
