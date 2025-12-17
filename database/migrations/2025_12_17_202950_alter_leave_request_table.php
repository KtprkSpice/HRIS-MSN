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
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->foreignId('leave_id')->after('employee_id')->constrained('leave_types')->onDelete('cascade');
            $table->dropColumn('leave_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {}
};
