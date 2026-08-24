<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LeaveTypeSeeder extends Seeder
{
    public function run(): void
    {
        $leaveTypes = [
            [
                'name' => 'Cuti Tahunan',
                'is_paid' => true,
                'deduction' => 0,
                'max_days' => 12,
                'limit_type' => 'yearly',
                'limit_days' => 12,
                'document' => 0,
            ],
            [
                'name' => 'Cuti Sakit',
                'is_paid' => true,
                'deduction' => 0,
                'limit_type' => 'monthly',
                'document' => 1,
                'max_days' => null,
                'limit_days' => 30,
            ],
            [
                'name' => 'Cuti Melahirkan',
                'is_paid' => true,
                'deduction' => 0,
                'limit_type' => 'yearly',
                'document' => 1,
                'max_days' => 90,
                'limit_days' => 90,
            ],
            [
                'name' => 'Cuti Keguguran',
                'is_paid' => true,
                'deduction' => 0,
                'limit_type' => 'yearly',
                'document' => 1,
                'max_days' => 45,
                'limit_days' => 45,
            ],
            [
                'name' => 'Cuti Haid',
                'is_paid' => true,
                'deduction' => 0,
                'limit_type' => 'monthly',
                'document' => 0,
                'max_days' => 2,
                'limit_days' => 2,
            ],
            [
                'name' => 'Izin Ibadah',
                'is_paid' => true,
                'deduction' => 0,
                'limit_type' => 'yearly',
                'document' => 0,
                'max_days' => null,
                'limit_days' => 30,
            ],
            [
                'name' => 'Izin Pribadi',
                'is_paid' => false,
                'limit_type' => 'yearly',
                'document' => 0,
                'deduction' => 100000,
                'max_days' => null,
                'limit_days' => 12,
            ],
            [
                'name' => 'Cuti Tidak Dibayar',
                'document' => 0,
                'is_paid' => false,
                'limit_type' => 'yearly',
                'deduction' => 150000,
                'max_days' => null,
                'limit_days' => 30,
            ],
        ];

        foreach ($leaveTypes as $leaveType) {
            DB::table('leave_types')->updateOrInsert(
                ['name' => $leaveType['name']],
                [
                    ...$leaveType,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}
