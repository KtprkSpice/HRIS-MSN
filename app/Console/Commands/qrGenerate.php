<?php

namespace App\Console\Commands;

use App\Models\QrCode;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Log;

class qrGenerate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:qr-generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Log::info('Generate QR START');

        $workDate = today();

        $tasks = Task::whereIn('status', ['on duty', 'pending'])->get();
        foreach ($tasks as $task) {
            foreach (['check_in', 'check_out'] as $type) {

                $existingQrCodes = QrCode::where('task_id', $task->id)
                    ->whereDate('date', $workDate)
                    ->where('type', $type)
                    ->where('is_active', true)
                    ->orderByDesc('id')
                    ->get();

                if ($existingQrCodes->isNotEmpty()) {
                    $existingQrCodes->skip(1)->each(function ($qrCode) {
                        $qrCode->update(['is_active' => false]);
                    });

                    continue;
                }

                $expiresAt = Carbon::now()->endOfDay();

                QrCode::create([
                    'task_id' => $task->id,
                    'token' => Str::uuid(),
                    'date' => $workDate,
                    'generated_at' => now(),
                    'expires_at' => $expiresAt,
                    'is_active' => 1,
                    'type' => $type,
                ]);
            }
        }

        Log::info('Generate QR END');

        $this->info('QR berhasil digenerate');

        return Command::SUCCESS;
    }
}
