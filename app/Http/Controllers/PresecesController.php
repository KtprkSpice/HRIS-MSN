<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Presence;
use App\Models\QrCode;
use App\Models\Schedule;
use App\Models\Task;
use Carbon\Carbon;
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

    private function resolveStatus($shift, $checkInTime)
    {
        $shiftStart = Carbon::parse(
            $checkInTime->toDateString().' '.$shift->start_time
        );

        if ($shift->cross_day && $shiftStart->greaterThan($checkInTime)) {
            $shiftStart->subDay();
        }

        $deadline = $shiftStart->addMinutes($shift->late_tolerance_minutes);

        return $checkInTime->lessThanOrEqualTo($deadline) ? 'on time' : 'late';

    }

    private function isWithinShiftTime($shift)
    {
        $now = now();

        $start = Carbon::parse(
            $now->toDateString().' '.$shift->start_time
        );

        $end = Carbon::parse(
            $now->toDateString().' '.$shift->end_time
        );

        if ($shift->cross_day && $end->lessThan($start)) {
            $end->addDay();
        }

        return $now->between(
            $start->subMinutes($shift->early_tolerance_minutes ?? 0),
            $end
        );
    }

    public function storeQr(Request $request)
    {
        \Log::info('DATA QR MASUK', $request->all());

        try {
            // 1️⃣ Validasi request
            if (! $request->qr_data || ! $request->task_id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'QR atau task tidak valid',
                ], 400);
            }

            $employee = auth()->user()->employee;

            if (! $employee) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User tidak terhubung ke employee',
                ], 403);
            }

            // 2️⃣ Ambil task & validasi employee
            $task = Task::with('employees')->findOrFail($request->task_id);

            if (! $task->employees->contains($employee->id)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda tidak terdaftar pada tugas ini',
                ], 403);
            }

            // 3️⃣ Ambil QR (per hari + per shift)
            $qr = QrCode::where('token', $request->qr_data)
                ->where('task_id', $task->id)
                ->first();

            if (! $qr) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'QR tidak valid atau sudah tidak aktif',
                ]);
            }

            $startOfWeek = now()->startOfWeek();
            $endOfWeek = now()->endOfWeek();
            // 4️⃣ Ambil schedule employee UNTUK SHIFT QR INI
            $schedule = Schedule::with('shift')
                ->where('employee_id', $employee->id)
                ->where('task_id', $task->id)
                ->where('shift_id', $qr->shift_id)
                ->first();

            if (! $schedule) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Ini bukan jadwal Anda',
                ]);
            }

            // 5️⃣ Ambil presence BERDASARKAN schedule_id (KUNCI)
            $presence = Presence::where('employee_id', $employee->id)
                ->where('schedule_id', $schedule->id)
                ->whereDate('date', today())
                ->first();

            // ======================
            // CHECK IN
            // ======================
            if ($qr->type === 'check_in') {

                if ($presence && $presence->check_in) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Anda sudah melakukan check-in',
                    ]);
                }

                $now = now();

                $shiftStart = now()->setTimeFromTimeString($schedule->shift->start_time);
                $shiftEnd = now()->setTimeFromTimeString($schedule->shift->end_time);

                // ⛔ Shift sudah lewat
                if ($now->greaterThan($shiftEnd)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Shift Anda sudah selesai',
                    ], 403);
                }

                // ⛔ Terlalu awal (opsional)
                if ($now->lessThan($shiftStart->subMinutes(30))) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Check-in belum dibuka',
                    ], 403);
                }

                $status = $this->resolveStatus($schedule->shift, now());

                Presence::create([
                    'employee_id' => $employee->id,
                    'task_id' => $task->id,
                    'schedule_id' => $schedule->id,
                    'shift_id' => $schedule->shift_id,
                    'date' => today(),
                    'check_in' => now(),
                    'check_out' => null,
                    'status' => $status,
                ]);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Check-in berhasil',
                    'data' => [
                        'status' => $status,
                    ],
                ]);
            }

            // ======================
            // CHECK OUT
            // ======================
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

                // 🔒 PASTI SHIFT SAMA
                if ($presence->shift_id !== $qr->shift_id) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'QR check-out tidak sesuai dengan shift check-in',
                    ]);
                }

                $presence->update([
                    'check_out' => now(),
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
            \Log::error('ERROR PRESENSI QR', [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan pada server',
            ], 500);
        }
    }
}
