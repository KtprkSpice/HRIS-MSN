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
        Schema::create('task_shift_rules', function (Blueprint $table) {
            $table->id();

            $table->foreignId('task_id')
                ->constrained('tasks')
                ->cascadeOnDelete();

            $table->foreignId('shift_id')
                ->constrained('shifts')
                ->cascadeOnDelete();

            $table->unsignedInteger('min_employee');

            $table->timestamps();

            $table->unique(
                ['task_id', 'shift_id'],
                'uq_task_shift_rule'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_shift_rules');
    }
};
