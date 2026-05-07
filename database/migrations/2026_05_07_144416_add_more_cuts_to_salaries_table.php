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
        Schema::table('salary', function (Blueprint $table) {
            $table->decimal('bpjs_kesehatan_cuts', 15, 2)->after('bonus');
            $table->decimal('bpjs_ketenagakerjaan_cuts', 15, 2)->after('bonus');
            $table->decimal('absent_cuts', 15, 2)->after('bonus');
            $table->decimal('late_cuts')->after('bonus');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salary', function (Blueprint $table) {
            //
        });
    }
};
