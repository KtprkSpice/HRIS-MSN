<?php

namespace Database\Seeders;

use Carbon\Carbon;
use faker\Factory as faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class employeeTaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = faker::create();
        foreach (range(1, 10) as $i) {
            DB::table('employees_tasks')->insert([
                'employee_id' => $faker->numberBetween(1, 10),
                'task_id' => $faker->numberBetween(1, 10),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
