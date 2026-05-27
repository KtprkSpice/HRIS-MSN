<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role_id' => 'owner',
        ]);

        $this->call([
            division_seeder::class,
            positionSeeder::class,
            RoleSeeder::class,
            EmployeOnlySeeder::class,
            employee_seeder::class,
            shiftSeeder::class,
            LeaveTypeSeeder::class,
            // LeaveSeeder::class,
            // SalarySeeder::class,
            TaskSeeder::class,
            employeeTaskSeeder::class,
            // SchedulesSeeder::class,
            // PresencesSeeder::class,
            TaskLocationSeeder::class,
            AllowanceSeeder::class,
            // AllowanceEmployeeSeeder::class,
        ]);
    }
}
