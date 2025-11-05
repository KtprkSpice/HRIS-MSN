<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use faker\factory as faker;
use Illuminate\Support\Carbon;

class employee_seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = faker::create('id_ID');

        $born_date = $faker->dateTimeBetween('-45 years', '-20 years')->format('Y-m-d');
        $hire_date = $faker->dateTimeBetween('-5 years', 'now')->format('Y-m-d');

        foreach (range(1, 10) as $i) {
            DB::table('employees')->insert([
                'fullname' => $faker->name(),
                'nik' => $faker->numerify('##########'),
                'division_id' => 1,
                'address' => $faker->address,
                'email' => $faker->unique()->safeEmail(),
                'user_id' => 1,
                'phone' => $faker->unique()->numerify('+62###########'),
                'hire_date' => $hire_date,
                'born_date' => $born_date,
                'bpjs_kesehatan' => $faker->numerify('##########'),
                'bpjs_ketenagakerjaan' => $faker->numerify('##########' ),
                'npwp' => $faker->numerify('##.###.###.#-###.###'),
                'status' => $faker->randomElement(['active', 'inactive']),
                'gender' => $faker->randomElement(['laki-laki', 'perempuan']),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
