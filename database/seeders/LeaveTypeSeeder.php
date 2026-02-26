<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LeaveTypeSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('leave_types')->insert([
            [
                'name' => 'Cuti Tahunan',
                'is_paid' => true,
                'deduction' => 0,
                'max_days' => 12,
                'limit_type' => 'yearly',
                'document' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Cuti Sakit',
                'is_paid' => true,
                'deduction' => 0,
                'limit_type' => 'monthly',
                'document' => 1,
                'max_days' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Izin Pribadi',
                'is_paid' => false,
                'limit_type' => 'yearly',
                'document' => 0,
                'deduction' => 100000,
                'max_days' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Cuti Tidak Dibayar',
                'document' => 0,
                'is_paid' => false,
                'limit_type' => 'yearly',
                'deduction' => 150000,
                'max_days' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
