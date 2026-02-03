<?php

namespace App\Http\Controllers;

use App\Models\QrCode;
use App\Models\Shift;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Support\Str;

class QrController extends Controller
{
    public function show(Task $task)
    {
        $workDate = today();

        // Ambil semua shift (karena QR by shift)
        $shifts = Shift::all();

        // Ambil QR hari ini untuk task ini
        $qrCodes = QrCode::where('task_id', $task->id)
            ->whereDate('date', $workDate)
            ->get()
            ->groupBy('shift_id');

        return view('presences.qr', compact('task', 'shifts', 'qrCodes'));
    }

    public function generate()
    {
        \Log::info('=== GENERATE QR BY SHIFT START ===');

        $workDate = today();

        // 1️⃣ Ambil task yang aktif
        $tasks = Task::whereIn('status', ['on duty', 'pending'])->get();

        \Log::info('Active tasks count', ['count' => $tasks->count()]);

        // 2️⃣ Ambil semua shift aktif
        $shifts = Shift::all();

        \Log::info('Active shifts count', ['count' => $shifts->count()]);

        foreach ($tasks as $task) {
            foreach ($shifts as $shift) {

                \Log::info('Processing task-shift', [
                    'task_id' => $task->id,
                    'shift_id' => $shift->id,
                ]);

                foreach (['check_in', 'check_out'] as $type) {

                    // 3️⃣ Cegah duplikasi QR
                    $exists = QrCode::where('task_id', $task->id)
                        ->where('shift_id', $shift->id)
                        ->whereDate('date', $workDate)
                        ->where('type', $type)
                        ->exists();

                    if ($exists) {
                        \Log::info('QR already exists, skipped', [
                            'task_id' => $task->id,
                            'shift_id' => $shift->id,
                            'type' => $type,
                        ]);

                        continue;
                    }

                    // 4️⃣ Hitung expired berdasarkan shift
                    $shiftEndDate = Carbon::parse($workDate);

                    if ($shift->cross_day) {
                        $shiftEndDate->addDay();
                    }

                    $shiftEndDateTime = Carbon::parse(
                        $shiftEndDate->format('Y-m-d').' '.$shift->end_time
                    );

                    $expiresAt = $shiftEndDateTime->addMinutes(50);

                    QrCode::create([
                        'task_id' => $task->id,
                        'shift_id' => $shift->id,
                        'token' => Str::uuid(),
                        'date' => $workDate,
                        'generated_at' => now(),
                        'expires_at' => $expiresAt,
                        'is_active' => true,
                        'type' => $type,
                    ]);

                    \Log::info('QR CREATED', [
                        'task_id' => $task->id,
                        'shift_id' => $shift->id,
                        'type' => $type,
                    ]);
                }
            }
        }

        \Log::info('=== GENERATE QR BY SHIFT END ===');

        return back()->with('success', 'QR berhasil digenerate');
    }
}
