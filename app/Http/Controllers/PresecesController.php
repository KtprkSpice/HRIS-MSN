<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Presence;
use App\Models\QrCode;
use App\Models\Schedule;
use App\Models\Shift;
use App\Models\Task;
use App\Models\Tasklocation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use function Illuminate\Support\now;
use function Laravel\Prompts\alert;

class PresecesController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $roles = $user->role->name;
        if ($roles === 'employee') {
            $presences = Presence::where('employee_id', $user->employee->id)->get();
        } else {
            $presences = Presence::all();
        }

        return view('presences.index', compact('presences'));
    }

    public function create()
    {
        $presences = Presence::all();
        $employees = Employee::all();
        $tasks = Task::all();
        $shifts = Shift::all();

        return view('presences.create', compact('presences', 'employees', 'tasks', 'shifts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required',
            'date' => 'required|date',
            'check_in' => 'nullable|date',
            'check_out' => 'nullable|date',
            'shift_id' => 'required|exists:shifts,id',
            'task_id' => 'required',
        ]);

        $shift = Shift::findOrFail($validated['shift_id']);

        $schedule = Schedule::create([
            'employee_id' => $validated['employee_id'],
            'shift_id' => $validated['shift_id'],
            'task_id' => $validated['task_id'],
            'date' => $validated['date'],
            'source' => 'manual',
        ]);

        // Default kalau gak ada check_in sama sekali -> absent
        $status = 'absent';
        $lateMinutes = 0;
        $workMinutes = 0;

        if (! empty($validated['check_in'])) {
            $checkIn = Carbon::parse($validated['check_in']);

            // Jadwal seharusnya masuk jam berapa (gabungin tanggal + jam shift)
            $scheduledStart = Carbon::parse($validated['date'] . ' ' . $shift->start_time);

            // Batas toleransi telat
            $toleranceLimit = $scheduledStart->copy()->addMinutes($shift->late_tolerance_minutes);

            if ($checkIn->lessThan($scheduledStart)) {
                return redirect()->back()
                    ->withInput() // WAJIB: agar data form tidak hilang saat balik ke halaman form
                    ->with('warning', 'Presensi tidak dapat dilakukan lebih awal dari jadwal shift!');
            }

            if ($checkIn->greaterThan($toleranceLimit)) {
                $status = 'late';
                $lateMinutes = $scheduledStart->diffInMinutes($checkIn);
            } else {
                $status = 'on time';
                $lateMinutes = 0;
            }

            // Hitung work_minutes kalau check_out ada
            if (! empty($validated['check_out'])) {
                $checkOut = Carbon::parse($validated['check_out']);

                // Handle shift cross_day (misal shift malam 22:00 - 06:00)
                if ($shift->cross_day && $checkOut->lessThan($checkIn)) {
                    $checkOut->addDay();
                }

                $workMinutes = $checkIn->diffInMinutes($checkOut);
            }
        }

        Presence::create([
            'employee_id' => $validated['employee_id'],
            'schedule_id' => $schedule->id,
            'shift_id' => $validated['shift_id'],
            'task_id' => $validated['task_id'],
            'date' => $validated['date'],
            'check_in' => $validated['check_in'] ?? null,
            'check_out' => $validated['check_out'] ?? null,
            'work_minutes' => $workMinutes,
            'late_minutes' => $lateMinutes,
            'status' => $status,
            'type' => 'outside',
        ]);

        return redirect()->route('presence.index')->with('success', 'Data Presensi Telah Dibuat');
    }

    public function edit(Presence $presence)
    {
        $presences = Presence::all();
        $employees = Employee::all();
        $tasks = Task::all();
        $shifts = Shift::all();

        return view('presences.edit', compact('employees', 'presence', 'shifts', 'tasks'));
    }

    public function update(Presence $presence, Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required',
            'task_id' => 'required',
            'shift_id' => 'required|exists:shifts,id',
            'date' => 'required|date',
            'check_in' => 'required|date',
            'check_out' => 'nullable|date',
        ]);

        $shift = Shift::findOrFail($validated['shift_id']);

        // Update schedule yang terkait (bukan bikin baru)
        $presence->schedule()->update([
            'employee_id' => $validated['employee_id'],
            'shift_id' => $validated['shift_id'],
            'task_id' => $validated['task_id'],
            'date' => $validated['date'],
        ]);

        // Hitung ulang status, late_minutes, work_minutes
        $status = 'absent';
        $lateMinutes = 0;
        $workMinutes = 0;

        if (! empty($validated['check_in'])) {
            $checkIn = Carbon::parse($validated['check_in']);
            $scheduledStart = Carbon::parse($validated['date'] . ' ' . $shift->start_time);
            $toleranceLimit = $scheduledStart->copy()->addMinutes($shift->late_tolerance_minutes);

            if ($checkIn->lessThan($scheduledStart)) {
                return redirect()->back()
                    ->withInput() // WAJIB: agar data form tidak hilang saat balik ke halaman form
                    ->with('warning', 'Presensi tidak dapat dilakukan lebih awal dari jadwal shift!');
            }

            if ($checkIn->greaterThan($toleranceLimit)) {
                $status = 'late';
                $lateMinutes = $scheduledStart->diffInMinutes($checkIn);
            } else {
                $status = 'on time';
            }

            if (! empty($validated['check_out'])) {
                $checkOut = Carbon::parse($validated['check_out']);

                // Handle shift cross_day (misal shift Malam 22:00 - 06:00)
                if ($shift->cross_day && $checkOut->lessThan($checkIn)) {
                    $checkOut->addDay();
                }

                $workMinutes = $checkIn->diffInMinutes($checkOut);
            }
        }

        $presence->update([
            'employee_id' => $validated['employee_id'],
            'shift_id' => $validated['shift_id'],
            'task_id' => $validated['task_id'],
            'date' => $validated['date'],
            'check_in' => $validated['check_in'],
            'check_out' => $validated['check_out'] ?? null,
            'work_minutes' => $workMinutes,
            'late_minutes' => $lateMinutes,
            'status' => $status,
        ]);

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

    // Location Validation backend
    private function calculatedDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6371000;

        $dLat = deg2rad($lat2 - $lat1);
        $dlng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dlng / 2) * sin($dlng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    public function storeQr(Request $request)
    {
        Log::info('Data Qr Masuk', $request->all());

        try {
            //  VALIDASI REQUEST
            if (! $request->qr_data || ! $request->task_id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'QR atau Task tidak valid',
                ], 400);
            }

            $employee = auth()->user()->employee;

            // Get Task Location
            $taskLocation = Tasklocation::where('task_id', $request->task_id)
                ->where('is_active', true)
                ->first();

            if (! $taskLocation) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Harap Nyalakan GPS',
                ]);
            }

            $distance = $this->calculatedDistance(
                $request->latitude,
                $request->longitude,
                $taskLocation->latitude,
                $taskLocation->longitude,
            );

            if ($distance > $taskLocation->radius) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda berada di luar radius',
                ]);
            }

            if (! $employee) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Harap login terlebih dahulu',
                ], 403);
            }

            $task = Task::with('employees')->findOrFail($request->task_id);
            $assignedEmployee = $task->employees()->where('employee_id', $employee->id)->exists();

            if (! $assignedEmployee) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda tidak terdaftar pada task ini',
                ]);
            }

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

            // Record untuk schedule aktif; dapat sudah dibuat oleh auto-absen.
            $presenceForSchedule = Presence::where('employee_id', $employee->id)
                ->where('schedule_id', $schedule->id)
                ->whereDate('date', today())
                ->first();

            // Gunakan record yang benar-benar memiliki check-in untuk check-out.
            // Jangan bergantung pada schedule_id saja, karena data lama dapat memiliki
            // lebih dari satu schedule atau record absent yang dibuat otomatis.
            $checkedInPresence = Presence::where('employee_id', $employee->id)
                ->where('task_id', $task->id)
                ->whereDate('date', today())
                ->whereNotNull('check_in')
                ->latest('check_in')
                ->first();

            $now = now();
            $shiftStart = today()->setTimeFromTimeString($schedule->shift->start_time);
            $shiftEnd = today()->setTimeFromTimeString($schedule->shift->end_time);

            // CHECK IN
            if ($qr->type === 'check_in') {

                if ($checkedInPresence) {
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
                // if ($now->lessThan($shiftStart->copy()->subMinutes(30))) {
                //     return response()->json([
                //         'status' => 'error',
                //         'message' => 'Presensi belum dibuka',
                //     ]);
                // }

                $status = 'on time';
                $lateMinutes = 0;

                if ($now->greaterThan($shiftStart)) {
                    $lateRaw = $shiftStart->diffInMinutes($now);
                    $lateMinutes = (int) ceil($lateRaw / 5) * 5;
                    $status = 'late';
                }

                $presenceData = [
                    'employee_id' => $employee->id,
                    'task_id' => $task->id,
                    'schedule_id' => $schedule->id,
                    'shift_id' => $schedule->shift_id,
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                    'date' => today(),
                    'check_in' => now(),
                    'late_minutes' => $lateMinutes,
                    'status' => $status,
                ];

                if ($presenceForSchedule) {
                    $presenceForSchedule->update($presenceData);
                } else {
                    Presence::create($presenceData);
                }

                return response()->json([
                    'status' => 'success',
                    'message' => 'Check-in berhasil',
                    'data' => compact('status'),
                ]);
            }

            // CHECK OUT
            if ($qr->type === 'check_out') {
                $presence = $checkedInPresence;

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
            Log::error('Error Presensi QR', [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan pada server',
            ], 500);
        }
    }
}
