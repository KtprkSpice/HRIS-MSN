<?php

namespace App\Http\Controllers;

use App\Models\QrCode;
use App\Models\Task;
use Carbon\Carbon;
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

    public function generate()
    {
        \Log::info('Generate QR START');

        $workDate = today();

        $tasks = Task::whereIn('status', ['on duty', 'pending'])->get();

        foreach ($tasks as $task) {
            foreach (['check_in', 'check_out'] as $type) {
                // Duplication Check
                $exsists = QrCode::where('task_id', $task->id)
                    ->whereDate('date', $workDate)
                    ->where('type', $type)
                    ->exists();

                if ($exsists) {
                    \Log::info('Qr exsists', [
                        'task_id' => $task->id,
                        'type' => $type,
                    ]);

                    continue;
                }

                $expiresAt = $type === 'check_in'
                ? Carbon::now()->endOfDay()
                : Carbon::now()->endOfDay()->addMinutes(30);

                QrCode::create([
                    'task_id' => $task->id,
                    'token' => Str::uuid(),
                    'date' => $workDate,
                    'generated_at' => now(),
                    'expires_at' => $expiresAt,
                    'is_active' => 1,
                    'type' => $type,
                ]);

                \Log::info('Qr Created', [
                    'task_id' => $task->id,
                    'type' => $type,
                ]);
            }
        }

        \Log::info('Generate Qr End');

        return back()->with('success', 'Qr Berhasil Digenerate');
    }
}
