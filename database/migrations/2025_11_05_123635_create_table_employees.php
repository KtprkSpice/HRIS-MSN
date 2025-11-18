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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('fullname');
            $table->string('nik', 16)->unique();
            $table->foreignId('division_id')->nullable()->constrained('division')->onDelete('cascade');
            $table->string('address')->nullable();
            $table->string('email')->unique();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('phone', 20)->unique();
            $table->date('hire_date');
            $table->date('born_date');
            $table->enum('gender', ['laki-laki','perempuan']);
            $table->string('bpjs_kesehatan',20)->nullable();
            $table->string('bpjs_ketenagakerjaan', 20)->nullable();
            $table->string('npwp', 20);
            $table->enum('status', ['active', 'inactive']);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_employees');
    }
};
