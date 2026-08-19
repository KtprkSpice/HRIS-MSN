<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // public function up(): void
    // {
    //     Schema::create('task_shift', function (Blueprint $table) {
    //         $table->id();
    //         $table->foreignId('task_id')->constrained('tasks')->cascadeOnDelete();
    //         $table->string('name');
    //         $table->time('start_time');
    //         $table->time('end_time');
    //         $table->unsignedSmallInteger('late_tolerance')->default(0);
    //         $table->timestamps();
    //         $table->softDeletes();
    //     });
    // }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_shift');
    }
};
