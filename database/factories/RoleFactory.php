<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Role>
 */
class RoleFactory extends Factory
{

protected $model = Role::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
       return [
            // Contoh nama role unik: "admin", "owner", "employee", "manager"
            'name' => $this->faker->unique()->randomElement(['owner', 'admin', 'employee']),
            'description' => $this->faker->sentence(),
        ];
    }
}
