<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use faker\Factory as faker;

class SalarySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $net_salary = $faker->randomFloat(2, 3500000, 4500000);
        $bonus = $faker->randomFloat(2, 0, 500000);
        $cuts = $faker->randomFloat(2, 0, 100000);
        foreach(range(1,10) as $i) {
            DB::table('salary')->insert([
                'employee_id' => $faker->numberBetween(1,10),
                'net_salary' => $net_salary,
                'bonus' => $bonus,
                'cuts' => $cuts,
                'date' => $faker->dateTimeBetween('-2 years', 'now'),
                'total' => $net_salary + $bonus - $cuts,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
