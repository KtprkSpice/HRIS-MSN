<?php

namespace Database\Seeders;

use Carbon\Carbon;
use faker\Factory as faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaskLocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $faker = faker::create();
        foreach (range(1, 10) as $i) {
            DB::table('task_location')->insert([
                'task_id' => $i,
                'name' => 'task location',
                'latitude' => -6.301015,
                'longitude' => 106.739563,
                'is_active' => true,
                'radius' => 100,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
