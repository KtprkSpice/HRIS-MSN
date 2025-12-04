<?php

namespace App\Http\Controllers;

use App\Models\QrCode;
use Endroid\QrCode\QrCode as qrGen;
use App\Models\Task;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\Request;

class QrController extends Controller
{
    public function show($taskId)
    {
        $task = Task::findOrFail($taskId);
        $qr = QrCode::where('task_id', $task->id)->where('date', today())->where('is_active', true)->firstOrFail();

        $qrText = $qr->token;

        $qrCode = new qrGen($qrText);

        $writer = new PngWriter();
        $result = $writer->write($qrCode);
        $base64 = base64_encode($result->getString());

        return view('presences.qr', [
            'task' => $task,
            'qr' => $qr,
            'qrBase64' => $base64,
        ]);
    }
}
