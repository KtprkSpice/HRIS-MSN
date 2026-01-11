<?php

namespace Database\Seeders;

use Carbon\Carbon;
use faker\Factory as faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PresencesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = faker::create();
        foreach (range(1, 10) as $i) {
            DB::table('presences')->insert([
                'employee_id' => $faker->numberBetween(1, 10),
                'task_id' => $faker->numberBetween(1, 10),
                'schedule_id' => $faker->numberBetween(1, 3),
                'date' => $faker->dateTimeBetween('-10 days', '-5 days')->format('Y-m-d'),
                'status' => $faker->randomElement(['on_time', 'late', 'invalid']),
                'type' => $faker->randomElement(['office', 'outside']),
                'check_in' => $faker->dateTimeBetween('-10 hour', '+1 hour'),
                'check_out' => $faker->dateTimeBetween('-3 hour', '+2 hour'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
