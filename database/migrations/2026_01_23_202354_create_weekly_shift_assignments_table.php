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
        Schema::create('weekly_shift_assignments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('employee_id')
                ->constrained('employees')
                ->cascadeOnDelete();

            $table->foreignId('task_id')
                ->constrained('tasks')
                ->cascadeOnDelete();

            $table->foreignId('shift_id')
                ->constrained('shifts')
                ->cascadeOnDelete();

            $table->date('week_start_date');

            $table->timestamps();
            $table->softDeletes();

            // 🔒 CUSTOM INDEX NAME (PENTING)
            $table->unique(
                ['employee_id', 'task_id', 'week_start_date'],
                'uq_weekly_shift_employee_task_week'
            );
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weekly_shift_assignments');
    }
};
