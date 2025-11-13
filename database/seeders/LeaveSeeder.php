<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use faker\Factory as faker;
use Illuminate\Support\Facades\DB;

class LeaveSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = faker::create();
        $leave_start = $faker->dateTimeBetween('-30 days', '-5 days')->format('Y-m-d');
        $leave_end = $faker->dateTimeBetween($leave_start, 'now')->format('Y-m-d');
        foreach(range(1,10) as $i ) {
            DB::table('leave_requests')->insert([
                'employee_id' => $faker->numberBetween(19, 28),
                'start_date' => $leave_start,
                'end_date' => $leave_end,
                'leave_type' => $faker->randomElement(['sick', 'vacation']),
                'status' => $faker->randomElement(['pending', 'confirmed', 'rejected']),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
