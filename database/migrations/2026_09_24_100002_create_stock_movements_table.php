<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Buku besar stok: append-only, tidak pernah di-update/delete.
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items')->restrictOnDelete();
            $table->date('movement_date');
            $table->string('direction', 3); // in / out
            $table->decimal('quantity_kg', 12, 2);
            $table->decimal('unit_cost', 15, 2)->nullable();
            $table->decimal('balance_after_kg', 12, 2);
            $table->morphs('source');
            $table->string('description')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->nullable();

            $table->index(['item_id', 'movement_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
