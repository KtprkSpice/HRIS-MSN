<?php

namespace App\Http\Controllers;

use App\Models\QrCode;
use App\Models\Shift;
use App\Models\Task;

class QrController extends Controller
{
    public function show(Task $task)
    {
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();

        // Ambil shift yang memang punya schedule di task ini (minggu ini)
        $shifts = Shift::whereHas('schedules', function ($q) use ($task, $startOfWeek, $endOfWeek) {
            $q->where('task_id', $task->id)
                ->whereBetween('date', [$startOfWeek, $endOfWeek]);
        })->get();

        // Ambil QR per shift (minggu ini)
        $qrCodes = QrCode::where('task_id', $task->id)
            ->whereBetween('date', [$startOfWeek, $endOfWeek])
            ->get()
            ->groupBy('shift_id');

        return view('presences.qr', compact('task', 'shifts', 'qrCodes'));
    }
}
