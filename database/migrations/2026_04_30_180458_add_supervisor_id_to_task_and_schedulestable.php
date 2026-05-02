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
        //     $table->foreignId('supervisor_id')->nullable()->after('team_leader_id')->constrained('employees')->nullOnDelete();
        //     $table->foreignId('shift_leader_id')->nullable()->after('team_leader_id')->constrained('employees')->nullOnDelete();
        // });

        // Schema::table('schedules', function (Blueprint $table) {
        //     $table->boolean('is_supervisor')->default(false)->after('source');
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::table('tasks', function (Blueprint $table) {
        //     $table->dropConstrainedForeignId('supervisor_id');
        //     $table->dropConstrainedForeignId('shift_leader_id');
        // });
        // Schema::table('schedules', function (Blueprint $table) {
        //     $table->dropColumn('is_supervisor');
        // });
    }
};
