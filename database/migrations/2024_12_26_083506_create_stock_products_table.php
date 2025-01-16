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
        Schema::create('stock_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_count_id')->constrained()->cascadeOnDelete(); // Relaciona com StockCount
            $table->string('name'); // Nome do produto
            $table->decimal('boning_stock', 10, 2)->default(0); // Estoque desossa
            $table->decimal('cashier_stock', 10, 2)->default(0)->nullable(); // Estoque caixaria
            $table->string('quality')->default(0)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_products');
    }
};
