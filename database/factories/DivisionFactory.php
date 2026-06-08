<?php

namespace Database\Factories;

use App\Models\Division;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Division>
 */
class DivisionFactory extends Factory
{
    protected $model = division::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
       return [
            // Contoh: "Human Resources", "Information Technology", "Marketing"
            'name' => $this->faker->unique()->jobTitle() . ' Department', 
            'description' => $this->faker->sentence(),
            'status' => $this->faker->randomElement(['Active', 'Inactive']),
        ];
    }
}
