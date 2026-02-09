<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Presence;
use App\Models\QrCode;
use App\Models\Schedule;
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
        \Log::info('Data Qr Masuk', $request->all());

        try {
            //  VALIDASI REQUEST
            if (! $request->qr_data || ! $request->task_id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'QR atau Task tidak valid',
                ], 400);
            }

            $employee = auth()->user()->employee;

            if (! $employee) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Harap login terlebih dahulu',
                ], 403);
            }

            $task = Task::with('employees')->findOrFail($request->task_id);

            if (! $task->employees->contains($employee->id)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda tidak terdaftar pada task ini',
                ], 403);
            }

            //  AMBIL QR (TANPA SHIFT)
            $qr = QrCode::where('token', $request->qr_data)
                ->where('task_id', $task->id)
                ->whereDate('date', today())
                ->where('is_active', true)
                ->first();

            if (! $qr) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'QR tidak valid atau tidak aktif',
                ]);
            }

            //  AMBIL SCHEDULE HARI INI
            $schedule = Schedule::with('shift')
                ->where('employee_id', $employee->id)
                ->where('task_id', $task->id)
                ->whereDate('date', today())
                ->first();

            if (! $schedule) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki jadwal hari ini',
                ]);
            }

            //  AMBIL PRESENCE
            $presence = Presence::where('employee_id', $employee->id)
                ->where('schedule_id', $schedule->id)
                ->whereDate('date', today())
                ->first();

            $now = now();
            $shiftStart = today()->setTimeFromTimeString($schedule->shift->start_time);
            $shiftEnd = today()->setTimeFromTimeString($schedule->shift->end_time);

            // CHECK IN
            if ($qr->type === 'check_in') {

                if ($presence && $presence->check_in) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Anda sudah melakukan check-in',
                    ]);
                }
                // Presesnbsi telat
                // if ($now->greaterThan($shiftStart->copy()->subMinutes(30))) {
                //     return response()->json([
                //         'status' => 'error',
                //         'message' => 'Anda sudah melewati batas jadwal anda',
                //     ]);
                // }

                // Presensi lebih awal
                if ($now->lessThan($shiftStart->copy()->subMinutes(30))) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Presensi belum dibuka',
                    ]);
                }

                $status = 'on time';
                $lateMinutes = 0;

                if ($now->greaterThan($shiftStart)) {
                    $lateRaw = $shiftStart->diffInMinutes($now);
                    $lateMinutes = (int) ceil($lateRaw / 10) * 10;
                    $status = 'late';
                }

                Presence::create([
                    'employee_id' => $employee->id,
                    'task_id' => $task->id,
                    'schedule_id' => $schedule->id,
                    'shift_id' => $schedule->shift_id,
                    'date' => today(),
                    'check_in' => now(),
                    'late_minutes' => $lateMinutes,
                    'status' => $status,
                ]);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Check-in berhasil',
                    'data' => compact('status'),
                ]);
            }

            // CHECK OUT
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

                $workMinutes = $presence->check_in->diffInMinutes($now);

                $presence->update([
                    'check_out' => now(),
                    'work_minutes' => $workMinutes,
                ]);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Check-out berhasil',
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Tipe QR tidak dikenali',
            ]);

        } catch (\Throwable $e) {
            \Log::error('Error Presensi QR', [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan pada server',
            ], 500);
        }
    }
}
