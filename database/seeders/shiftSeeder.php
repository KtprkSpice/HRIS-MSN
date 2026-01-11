<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class shiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('shifts')->insert([
            [
                'name' => 'Pagi',
                'start_time' => '08:00:00',
                'end_time' => '16:00:00',
                'cross_day' => false,
                'late_tolerance_minutes' => 10,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Sore',
                'start_time' => '16:00:00',
                'end_time' => '00:00:00',
                'cross_day' => true,
                'late_tolerance_minutes' => 10,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Malam',
                'start_time' => '22:00:00',
                'end_time' => '06:00:00',
                'cross_day' => true,
                'late_tolerance_minutes' => 15,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
