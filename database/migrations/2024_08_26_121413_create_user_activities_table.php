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
        Schema::create('user_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('activity_id')->constrained();
            $table->date('assigned_date');
            $table->longText('observation')->nullable();
            $table->longText('observation_reject')->nullable();
            $table->enum('status', ['concluido', 'em_analise', 'nao_aprovado', 'aprovado', 'nao_concluido'])->default('em_analise');
            $table->array('attachments');
            $table->foreignId('department_master_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_activities');
    }
};
