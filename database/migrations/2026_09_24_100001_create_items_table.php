<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique();
            $table->string('name');
            $table->string('type'); // App\Enums\ItemType
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->decimal('weight_per_sack_kg', 8, 2)->default(25);
            // Cache saldo stok, hanya diubah lewat StockService.
            $table->decimal('stock_kg', 12, 2)->default(0);
            // Biaya rata-rata bergerak (moving average) per kg.
            $table->decimal('avg_cost_per_kg', 15, 2)->default(0);
            $table->decimal('default_price_per_kg', 15, 2)->nullable();
            $table->decimal('min_stock_kg', 12, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
