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
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->json('work_days'); // JSON array to store the work days
            $table->boolean('is_scale')->default(0);
            $table->json('holidays')->nullable();
            $table->integer('office_day');
            $table->foreignId('enterprise_id')->constrained('enterprises')->nullable();
            $table->foreignId('department_master_id')->constrained();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
