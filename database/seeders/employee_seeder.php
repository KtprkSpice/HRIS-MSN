<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\User;
use faker\factory as faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

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

            $user = User::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail(),
                'password' => Hash::make('password'),
                'role_id' => $faker->numberBetween(1, 3),
            ]);

            Employee::Create([
                'fullname' => $faker->name,
                'nik' => $faker->numerify('##########'),
                'position_id' => $faker->numberBetween(1, 7),
                'division_id' => $faker->numberBetween(1, 3),
                'address' => $faker->address,
                'email' => $user->email,
                'user_id' => $user->id,
                'phone' => $faker->unique()->numerify('+62###########'),
                'hire_date' => $hire_date,
                'born_date' => $born_date,
                'bpjs_kesehatan' => $faker->numerify('##########'),
                'bpjs_ketenagakerjaan' => $faker->numerify('##########'),
                'npwp' => $faker->numerify('##.###.###.#-###.###'),
                'status' => $faker->randomElement(['active', 'inactive']),
                'gender' => $faker->randomElement(['laki-laki', 'perempuan']),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
