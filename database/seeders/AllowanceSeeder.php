<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AllowanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('allowances')->insert([
            [
                'allowance_type' => 'BPJS Kesehatan',
                'calculation_type' => 'fixed',
                'percentage_value' => null,
                'amount' => 100000,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'allowance_type' => 'BPJS Ketenagakerjaan',
                'percentage_value' => 2.70,
                'amount' => null,
                'calculation_type' => 'percentage',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
