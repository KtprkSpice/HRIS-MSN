<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Schema::table('task_shift_rules', function (Blueprint $table) {
        //     $table->foreignId('shift_leader_id')
        //         ->nullable()
        //         ->after('min_employee')
        //         ->constrained('employees')
        //         ->nullOnDelete();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::table('task_shift_rules', function (Blueprint $table) {
        //     $table->dropConstrainedForeignId('shift_leader_id');
        // });
    }
};
