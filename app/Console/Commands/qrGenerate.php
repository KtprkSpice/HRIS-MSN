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

                $exists = QrCode::where('task_id', $task->id)
                    ->whereDate('date', $workDate)
                    ->where('type', 'on duty')
                    ->exists();

                if ($exists) {
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
            }
        }

        Log::info('Generate QR END');

        $this->info('QR berhasil digenerate');

        return Command::SUCCESS;
    }
}
