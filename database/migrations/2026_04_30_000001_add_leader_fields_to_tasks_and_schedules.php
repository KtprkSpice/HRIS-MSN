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
        // Schema::table('tasks', function (Blueprint $table) {
        //     $table->foreignId('team_leader_id')
        //         ->nullable()
        //         ->after('status')
        //         ->constrained('employees')
        //         ->nullOnDelete();
        // });

        // Schema::table('schedules', function (Blueprint $table) {
        //     $table->boolean('is_shift_leader')->default(false)->after('source');
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::table('schedules', function (Blueprint $table) {
        //     $table->dropColumn('is_shift_leader');
        // });

        // Schema::table('tasks', function (Blueprint $table) {
        //     $table->dropConstrainedForeignId('team_leader_id');
        // });
    }
};
