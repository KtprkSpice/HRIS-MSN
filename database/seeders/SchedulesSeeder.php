<?php

namespace Database\Seeders;

use Carbon\Carbon;
use faker\Factory as faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SchedulesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        foreach (range(1, 10) as $i) {
            DB::table('schedules')->insert([
                'employee_id' => $faker->numberBetween(1, 10),
                'shift_id' => $faker->numberBetween(1, 3),
                'task_id' => $faker->numberBetween(1, 10),
                'date' => $faker->dateTimeBetween('-1 years', '-5 days')->format('Y-m-d'),
                'source' => $faker->randomElement(['manual', 'system', 'swap']),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
