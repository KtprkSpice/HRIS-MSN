<?php

namespace Database\Factories;

use App\Models\Division;
use App\Models\Position;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Position>
 */
class PositionFactory extends Factory
{
    protected $model = Position::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->jobTitle(),
            // Otomatis membuat Division baru jika tidak didefinisikan saat dipanggil
            'division_id' => Division::factory(), 
            // Gaji pokok (misal kisaran 5.000.000 sampai 15.000.000)
            'base_salary' => $this->faker->numberBetween(5000000, 15000000),
            // Potongan per menit keterlambatan (misal kisaran 1.000 sampai 5.000)
            'cut_per_minute' => $this->faker->numberBetween(1000, 5000),
        ];
    }
}
