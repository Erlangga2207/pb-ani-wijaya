<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Tabel stok generik lama (v1) diganti buku besar stock_movements + dokumen transaksi.
// Data lama dikonfirmasi dummy (web belum live) sebelum dihapus.
return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('stock_transactions');
    }

    public function down(): void
    {
        Schema::create('stock_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->date('transaction_date');
            $table->string('reference_number')->nullable();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->restrictOnDelete();
            $table->foreignId('buyer_id')->nullable()->constrained('buyers')->restrictOnDelete();
            $table->decimal('quantity_kg', 12, 2);
            $table->decimal('input_quantity', 12, 2);
            $table->string('input_unit');
            $table->decimal('price_per_kg', 12, 2)->nullable();
            $table->decimal('total_price', 14, 2)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }
};
