<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Presence;
use App\Models\QrCode;
use App\Models\Schedule;
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
        if (auth()->user()->role->name === 'employee') {
            abort(403);
        }

        $employees = Employee::where('status', 'active')->get();
        $scheduleOptions = $this->manualPresenceScheduleOptions();

        return view('presences.create', compact('employees', 'scheduleOptions'));
    }

    public function store(Request $request)
    {
        if (auth()->user()->role->name === 'employee') {
            abort(403);
        }

        $validated = $request->validate([
            'employee_id' => 'required',
            'date' => 'required|date',
            'check_in' => 'nullable|date',
            'check_out' => 'nullable|date',
            'shift_id' => 'required|exists:shifts,id',
            'task_id' => 'required',
        ]);

        $schedule = $this->findManualPresenceSchedule($validated);

        if (! $schedule) {
            return back()->withInput()->with('warning', 'Kehadiran manual harus mengikuti jadwal yang sudah diimport/dibuat. Pilih karyawan dan tanggal yang memiliki jadwal aktif.');
        }

        if (Presence::where('schedule_id', $schedule->id)->exists()) {
            return back()->withInput()->with('warning', 'Presensi untuk jadwal ini sudah ada.');
        }

        $shift = $schedule->shift;

        // Default kalau gak ada check_in sama sekali -> absent
        $status = 'absent';
        $lateMinutes = 0;
        $workMinutes = 0;

        if (! empty($validated['check_in'])) {
            $checkIn = Carbon::parse($validated['check_in']);
            $scheduleDate = Carbon::parse($schedule->date)->toDateString();

            if ($checkIn->toDateString() !== $scheduleDate) {
                return redirect()->back()
                    ->withInput()
                    ->with('warning', 'Jam masuk harus berada di tanggal jadwal yang dipilih.');
            }

            // Jadwal seharusnya masuk jam berapa (gabungin tanggal + jam shift)
            $scheduledStart = Carbon::parse($scheduleDate . ' ' . $shift->start_time);

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

                if (! $shift->cross_day && $checkOut->lessThan($checkIn)) {
                    return redirect()->back()
                        ->withInput()
                        ->with('warning', 'Jam keluar tidak boleh lebih awal dari jam masuk.');
                }

                $workMinutes = $checkIn->diffInMinutes($checkOut);
            }
        }

        Presence::create([
            'employee_id' => $validated['employee_id'],
            'schedule_id' => $schedule->id,
            'shift_id' => $schedule->shift_id,
            'task_id' => $schedule->task_id,
            'date' => $schedule->date,
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
        if (auth()->user()->role->name === 'employee') {
            abort(403);
        }

        $employees = Employee::where('status', 'active')->get();
        $scheduleOptions = $this->manualPresenceScheduleOptions($presence);

        return view('presences.edit', compact('employees', 'presence', 'scheduleOptions'));
    }

    public function update(Presence $presence, Request $request)
    {
        if (auth()->user()->role->name === 'employee') {
            abort(403);
        }

        $validated = $request->validate([
            'employee_id' => 'required',
            'task_id' => 'required',
            'shift_id' => 'required|exists:shifts,id',
            'date' => 'required|date',
            'check_in' => 'required|date',
            'check_out' => 'nullable|date',
        ]);

        $schedule = $this->findManualPresenceSchedule($validated);

        if (! $schedule) {
            return back()->withInput()->with('warning', 'Kehadiran manual harus mengikuti jadwal yang sudah diimport/dibuat. Pilih karyawan dan tanggal yang memiliki jadwal aktif.');
        }

        $hasOtherPresence = Presence::where('schedule_id', $schedule->id)
            ->where('id', '!=', $presence->id)
            ->exists();

        if ($hasOtherPresence) {
            return back()->withInput()->with('warning', 'Presensi untuk jadwal ini sudah ada.');
        }

        $shift = $schedule->shift;

        // Hitung ulang status, late_minutes, work_minutes
        $status = 'absent';
        $lateMinutes = 0;
        $workMinutes = 0;

        if (! empty($validated['check_in'])) {
            $checkIn = Carbon::parse($validated['check_in']);
            $scheduleDate = Carbon::parse($schedule->date)->toDateString();

            if ($checkIn->toDateString() !== $scheduleDate) {
                return redirect()->back()
                    ->withInput()
                    ->with('warning', 'Jam masuk harus berada di tanggal jadwal yang dipilih.');
            }

            $scheduledStart = Carbon::parse($scheduleDate . ' ' . $shift->start_time);
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

                if (! $shift->cross_day && $checkOut->lessThan($checkIn)) {
                    return redirect()->back()
                        ->withInput()
                        ->with('warning', 'Jam keluar tidak boleh lebih awal dari jam masuk.');
                }

                $workMinutes = $checkIn->diffInMinutes($checkOut);
            }
        }

        $presence->update([
            'employee_id' => $validated['employee_id'],
            'schedule_id' => $schedule->id,
            'shift_id' => $schedule->shift_id,
            'task_id' => $schedule->task_id,
            'date' => $schedule->date,
            'check_in' => $validated['check_in'],
            'check_out' => $validated['check_out'] ?? null,
            'work_minutes' => $workMinutes,
            'late_minutes' => $lateMinutes,
            'status' => $status,
        ]);

        return redirect()->route('presence.index')->with('success', 'Data Berhasil Diubah');
    }

    private function manualPresenceScheduleOptions(?Presence $presence = null)
    {
        $currentScheduleId = $presence?->schedule_id;

        return Schedule::with(['task', 'shift'])
            ->whereHas('employee', function ($query) {
                $query->where('status', 'active');
            })
            ->where(function ($query) use ($currentScheduleId) {
                $query->whereHas('task', function ($taskQuery) {
                    $taskQuery->whereIn('status', ['pending', 'on duty']);
                });

                if ($currentScheduleId) {
                    $query->orWhere('id', $currentScheduleId);
                }
            })
            ->get()
            ->filter(fn ($schedule) => $schedule->task && $schedule->shift)
            ->map(function ($schedule) {
                $startTime = Carbon::parse($schedule->shift->start_time)->format('H:i');
                $endTime = Carbon::parse($schedule->shift->end_time)->format('H:i');

                return [
                    'schedule_id' => (string) $schedule->id,
                    'employee_id' => (string) $schedule->employee_id,
                    'date' => Carbon::parse($schedule->date)->toDateString(),
                    'task_id' => (string) $schedule->task_id,
                    'task_name' => $schedule->task->name,
                    'shift_id' => (string) $schedule->shift_id,
                    'shift_name' => "{$schedule->shift->name} ({$startTime} - {$endTime})",
                ];
            })
            ->values();
    }

    private function findManualPresenceSchedule(array $validated): ?Schedule
    {
        return Schedule::with(['task', 'shift'])
            ->where('employee_id', $validated['employee_id'])
            ->where('task_id', $validated['task_id'])
            ->where('shift_id', $validated['shift_id'])
            ->whereDate('date', $validated['date'])
            ->whereHas('employee', function ($query) {
                $query->where('status', 'active');
            })
            ->whereHas('task', function ($query) {
                $query->whereIn('status', ['pending', 'on duty']);
            })
            ->first();
    }

    public function destroy(Presence $presence)
    {
        if (auth()->user()->role->name === 'employee') {
            abort(403);
        }

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

            if (! $qr || Carbon::parse($qr->expires_at)->lt(now())) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'QR tidak valid, tidak aktif, atau sudah expired',
                ]);
            }

            // Gunakan record yang benar-benar memiliki check-in untuk check-out.
            // Jangan bergantung pada schedule_id saja, karena data lama dapat memiliki
            // lebih dari satu schedule atau record absent yang dibuat otomatis.
            $checkedInPresence = Presence::with('schedule.shift')
                ->where('employee_id', $employee->id)
                ->where('task_id', $task->id)
                ->whereBetween('date', [
                    today()->copy()->subDay()->toDateString(),
                    today()->toDateString(),
                ])
                ->whereNotNull('check_in')
                ->whereNull('check_out')
                ->latest('check_in')
                ->first();

            if ($qr->type === 'check_out' && $checkedInPresence?->schedule) {
                $schedule = $checkedInPresence->schedule;
            } else {
                $schedule = Schedule::with('shift')
                    ->where('employee_id', $employee->id)
                    ->where('task_id', $task->id)
                    ->whereDate('date', today())
                    ->first();
            }

            if (! $schedule) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki jadwal hari ini',
                ]);
            }

            // Record untuk schedule aktif; dapat sudah dibuat oleh auto-absen.
            $presenceForSchedule = Presence::where('employee_id', $employee->id)
                ->where('schedule_id', $schedule->id)
                ->whereDate('date', $schedule->date)
                ->first();

            $now = now();
            $shiftStart = Carbon::parse($schedule->date)->setTimeFromTimeString($schedule->shift->start_time);

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

                $toleranceLimit = $shiftStart->copy()->addMinutes($schedule->shift->late_tolerance_minutes);

                if ($now->greaterThan($toleranceLimit)) {
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
                    'date' => $schedule->date,
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
