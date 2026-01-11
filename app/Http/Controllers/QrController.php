<?php

namespace App\Http\Controllers;

use App\Models\QrCode;
use App\Models\Task;

class QrController extends Controller
{
    public function show(Task $task)
    {

        $qrCheckin = QrCode::where('task_id', $task->id)->where('date', today())->where('type', 'check_in')->where('is_active', true)->first();

        $qrCheckout = QrCode::where('task_id', $task->id)->where('date', today())->where('type', 'check_out')->where('is_active', true)->first();

        return view('presences.qr', compact('task', 'qrCheckin', 'qrCheckout'));
    }
}
