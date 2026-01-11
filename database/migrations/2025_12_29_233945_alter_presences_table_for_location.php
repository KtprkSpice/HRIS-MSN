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
        Schema::table('presences', function (Blueprint $table) {
            $table->decimal('latitude', 10, 7)->nullable()->after('date');
            $table->decimal('longitude', 10, 7)->nullable()->after('date');
            $table->decimal('distance', 8, 2)->after('date')->nullable();
            $table->enum('type', ['office', 'outside'])->after('date');
            $table->enum('status', ['on_time', 'late', 'invalid'])->after('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('presences', function (Blueprint $table) {
            //
        });
    }
};
