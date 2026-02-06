<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Faker\Factory as faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class positionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $positions = [
            [
                'name' => 'Security Officer',
                'division_id' => 2,
                'base_salary' => 4500000,
            ],
            [
                'name' => 'Shift Leader',
                'division_id' => 2,
                'base_salary' => 5000000,
            ],
            [
                'name' => 'Chief Security',
                'division_id' => 2,
                'base_salary' => 5500000,
            ],
            [
                'name' => 'Cleaning Staff',
                'division_id' => 3,
                'base_salary' => 4000000,
            ],
            [
                'name' => 'Team Leader',
                'division_id' => 2,
                'base_salary' => 5000000,

            ],
            [
                'name' => 'Parking Staff',
                'division_id' => 4,
                'base_salary' => 4000000,
            ],
            [
                'name' => 'Parking Supervisor',
                'division_id' => 4,
                'base_salary' => 5000000,
            ],

        ];

        $faker = faker::create();
        foreach ($positions as $position) {
            DB::table('positions')->insert([
                'name' => $position['name'],
                'division_id' => $position['division_id'],
                'base_salary' => $position['base_salary'],
                'cut_per_minute' => 5000,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
