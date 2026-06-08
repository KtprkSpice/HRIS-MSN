<?php

namespace Database\Factories;

use App\Models\Division;
use App\Models\Employee;
use App\Models\Position;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Employee>
 */
class EmployeeFactory extends Factory
{
    protected $model = Employee::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "fullname" => $this->faker->name(),
            'nik' => $this->faker->unique()->numerify('################'),
            'division_id' => Division::inRandomOrder()->first()?->id ?? 1,
            'position_id' => Position::inRandomOrder()->first()?->id ?? 1,
            'address' => $this->faker->address(),
            'email' => $this->faker->unique()->safeEmail(),
            // Membuat user login baru secara otomatis untuk relasi ini
            'user_id' => User::factory(), 
            // Nomor telepon Indonesia tiruan
            'phone' => $this->faker->phoneNumber(), 
            'hire_date' => $this->faker->date('Y-m-d', 'now'),
            // Born date diatur acak untuk umur kisaran 20 - 50 tahun lalu
            'born_date' => $this->faker->date('Y-m-d', '-20 years'), 
            'gender' => $this->faker->randomElement(['laki-laki', 'perempuan']),
            // Nomor BPJS biasanya 11-13 digit
            'bpjs_kesehatan' => $this->faker->numerify('#############'),
            'bpjs_ketenagakerjaan' => $this->faker->numerify('#############'),
            // NPWP 15 digit angka
            'npwp' => $this->faker->numerify('###############'),
            'status' => $this->faker->randomElement(['active', 'inactive']),
        ];
    }
}
