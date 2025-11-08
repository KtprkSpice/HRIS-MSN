<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use faker\Factory as faker;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = faker::create();
        $task_start = $faker->dateTimeBetween('-20 days', '-5 days')->format('Y-m-d');
        $task_end = $faker->dateTimeBetween('-10 days', '-1 days')->format('Y-m-d');
        foreach(range(1, 10) as $i) {
            DB::table('tasks')->insert([
            'name' => $faker->title(),
            'description' => $faker->text(),
            'start_time' => $task_start,
            'end_time' => $task_end,
            'status' => $faker->randomElement(['done', 'on duty', 'pending']),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            ]);
        };
    }
}
