<?php

namespace Database\Seeders;

use App\Models\Division;
use App\Models\Employee;
use App\Models\Position;
use App\Models\Role;
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

        $divisions = Division::factory()->count(3)->create();

        // 2. Buat data master: 3 Position
        // Kita sebar 3 position ini ke division yang baru saja dibuat
        $positions = collect();
        foreach ($divisions as $index => $division) {
            $positions->push(
                Position::factory()->create([
                    'division_id' => $division->id,
                ])
            );
        }
        // User::factory(10)->create();

        $ownerRole = Role::factory()->create([
            'name' => 'owner',
            'description' => 'Sistem Owner dengan akses penuh',
        ]);

        // Opsional: Buat beberapa role tambahan lainnya agar total ada beberapa pilihan role
        Role::factory()->create(['name' => 'hr']);
        Role::factory()->create(['name' => 'employee']);

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role_id' => $ownerRole->id,
        ]);

        Employee::factory()->create([
            'user_id' => $user->id,
            'division_id' => $divisions->first()->id,
            'position_id' => $positions->first()->id,
            'fullname' => $user->name,
            'email' => $user->email,
        ]);

        $this->call([
            //     division_seeder::class,
            //     positionSeeder::class,
            // //     RoleSeeder::class,
            EmployeOnlySeeder::class,
            employee_seeder::class,
            shiftSeeder::class,
            LeaveTypeSeeder::class,
            // //     // LeaveSeeder::class,
            // //     // SalarySeeder::class,
            TaskSeeder::class,
            employeeTaskSeeder::class,
            // //     // SchedulesSeeder::class,
            // //     // PresencesSeeder::class,
            TaskLocationSeeder::class,
            AllowanceSeeder::class,
            // //     // AllowanceEmployeeSeeder::class,
        ]);
    }
}
