<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Presence;
use App\Models\QrCode;
use App\Models\Task;
use Illuminate\Http\Request;

use function Illuminate\Support\now;

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
            if (! $request->qr_data || ! $request->task_id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'QR atau task tidak valid',
                ], 400);
            }

            $employee = auth()->user()->employee;
            $task = Task::with('employees')->findOrFail($request->task_id);

            if (! $task->employees->contains($employee->id)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda tidak terdaftar pada tugas ini',
                ]);
            }

            $qr = QrCode::where('token', $request->qr_data)->where('task_id', $task->id)->whereDate('date', today())->where('is_active', true)->first();

            if (! $qr) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Qr tidak valid atau ladaluarsa',
                ]);
            }

            if (now()->greaterThan($qr->expires_at)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Qr sudah kadaluarsa',
                ]);
            }

            $presence = Presence::where('employee_id', $employee->id)->where('task_id', $task->id)->whereDate('date', today())->first();

            if ($qr->type === 'check_in') {

                if ($presence && $presence->check_in) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Anda sudah melakukan check-in',
                    ]);
                }

                Presence::create([
                    'employee_id' => $employee->id,
                    'task_id' => $task->id,
                    'date' => today(),
                    'check_in' => now(),
                    'check_out' => null,
                    'type' => 'outside',
                ]);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Check-in berhasil',
                ]);
            }

            if ($qr->type === 'check_out') {
                if (! $presence || ! $presence->check_in) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Anda belum melakukan check-in',
                    ]);
                }

                if ($presence->check_out) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Anda sudah melakukan check-out',
                    ]);
                }

                $presence->update([
                    'check_out' => now(),
                    'type' => 'outside',
                ]);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Check-out berhasil',
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Tipe Qr tidak dikenali',
            ]);
        } catch (\Throwable $e) {
            \Log::error('ERROR PRESENSI: '.$e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'server error.',
            ], 500);
        }
    }
}
