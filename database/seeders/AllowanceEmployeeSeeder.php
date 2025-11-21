<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as faker;

class AllowanceEmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = faker::create();
        foreach(range(1, 10) as $i) {
            DB::table('allowance_employee')->insert([
                'employee_id' => $faker->numberBetween(1, 10),
                'allowance_id' => $faker->numberBetween(1,2),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
