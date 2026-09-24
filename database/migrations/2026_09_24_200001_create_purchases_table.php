<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->date('purchase_date');
            $table->foreignId('supplier_id')->constrained('suppliers')->restrictOnDelete();
            $table->foreignId('item_id')->constrained('items')->restrictOnDelete();
            $table->decimal('input_quantity', 12, 2);
            $table->string('input_unit');
            $table->decimal('quantity_kg', 12, 2);
            $table->decimal('moisture_percent', 5, 2)->nullable();
            $table->decimal('price_per_kg', 15, 2);
            $table->decimal('subtotal', 15, 2);
            $table->decimal('other_cost', 15, 2)->default(0);
            $table->decimal('total', 15, 2);
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

            $table->index('purchase_date');
            $table->index('payment_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
