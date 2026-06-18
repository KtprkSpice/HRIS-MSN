<?php

namespace App\Http\Controllers;

use App\Models\QrCode;
use App\Models\Schedule;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

use function Symfony\Component\Clock\now;

class QrController extends Controller
{
    public function show(Task $task)
    {
        $workDate = today();

        // Ambil QR hari ini untuk task ini
        $qrCodes = QrCode::where('task_id', $task->id)
            ->whereDate('date', $workDate)
            ->where('is_active', true)
            ->get()
            ->keyBy('type');

        $qr = QrCode::where('task_id', $task->id)
            ->whereDate('date', $workDate)
            ->where('is_active', true)
            ->first();

        return view('presences.qr', compact('task', 'qrCodes', 'qr'));
    }

    // public function generate()
    // {

    //     // Genetae Schedule)
    //     Log::info('Start Scheduler');
    //     Artisan::call('auto-absent');
    //     Artisan::call('auto-schedule');
    //     Log::info('End Scheduler');
    //     \Log::info('Generate QR START');

    //     $workDate = today();

    //     $tasks = Task::whereIn('status', ['on duty', 'pending'])->get();

    //     foreach ($tasks as $task) {
    //         foreach (['check_in', 'check_out'] as $type) {
    //             // Duplication Check
    //             $exsists = QrCode::where('task_id', $task->id)
    //                 ->whereDate('date', $workDate)
    //                 ->where('type', $type)
    //                 ->exists();

    //             if ($exsists) {
    //                 \Log::info('Qr exsists', [
    //                     'task_id' => $task->id,
    //                     'type' => $type,
    //                 ]);

    //                 continue;
    //             }

    //             $expiresAt = $type === 'check_in'
    //             ? Carbon::now()->endOfDay()
    //             : Carbon::now()->endOfDay()->addMinutes(30);

    //             QrCode::create([
    //                 'task_id' => $task->id,
    //                 'token' => Str::uuid(),
    //                 'date' => $workDate,
    //                 'generated_at' => now(),
    //                 'expires_at' => $expiresAt,
    //                 'is_active' => 1,
    //                 'type' => $type,
    //             ]);

    //             \Log::info('Qr Created', [
    //                 'task_id' => $task->id,
    //                 'type' => $type,
    //             ]);
    //         }
    //     }

    //     \Log::info('Generate Qr End');

    //     return back()->with('success', 'Qr Berhasil Digenerate');
    // }

    public function generate()
    {
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();

        $scheduleExists = Schedule::whereBetween('date', [
            $weekStart->toDateString(),
            $weekEnd->toDateString(),
        ])->exists();

        if (! $scheduleExists) {
            Artisan::call('app:auto-schedule');
        }

        Artisan::call('app:qr-generate');

        Artisan::call('app:auto-absent');

        return back()->with(
            'success',
            'Generate operasional berhasil dijalankan'
        );
    }
}
