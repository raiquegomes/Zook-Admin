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
        Schema::create('enterprises', function (Blueprint $table) {
            $table->id();
            $table->string('cnpj')->unique()->nullable();
            $table->string('name')->nullable();
            $table->string('fantasy_name')->nullable();
            $table->string('email')->unique();
            $table->string('slug')->unique()->nullable();
            $table->string('token_invitation')->unique()->nullable();
            $table->boolean('is_active');
            $table->timestamps();
        });

        Schema::create('enterprise_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enterprise_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enterprises');
        Schema::dropIfExists('enterprise_user');
    }
};
