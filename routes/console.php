<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// php artisan schedule:run
$start = today()->startOfWeek();
Schedule::command('app:auto-schedule')
    ->weekly($start)
    ->withoutOverlapping();

Schedule::command('app:qr-generate')
    ->dailyAt('00:00')
    ->withoutOverlapping();

Schedule::command('app:auto-absent')
    ->dailyAt('00:00')
    ->withoutOverlapping();

Schedule::command('app:generate-salary')
    ->everyMinute()
    ->withoutOverlapping();

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
