<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->date('sale_date');
            $table->foreignId('buyer_id')->constrained('buyers')->restrictOnDelete();
            $table->decimal('subtotal', 15, 2);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('shipping_cost', 15, 2)->default(0); // ditagihkan ke pembeli, bukan omzet barang
            $table->decimal('total', 15, 2);
            $table->decimal('cogs_total', 15, 2); // HPP, hanya owner+
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->string('payment_status')->default('unpaid');
            $table->date('due_date')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->default('posted');
            $table->text('void_reason')->nullable();
            $table->timestamp('voided_at')->nullable();
            $table->foreignId('voided_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('sale_date');
            $table->index('payment_status');
        });

        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained('sales')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->restrictOnDelete();
            $table->decimal('input_quantity', 12, 2);
            $table->string('input_unit');
            $table->decimal('quantity_kg', 12, 2);
            $table->decimal('price_per_kg', 15, 2);
            $table->decimal('line_total', 15, 2);
            $table->decimal('cogs_per_kg', 15, 2);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_items');
        Schema::dropIfExists('sales');
    }
};
