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
    Schema::create('stock_transactions', function (Blueprint $table) {
        $table->id();

        // Tipe transaksi: in (barang masuk) atau out (barang keluar)
        $table->enum('type', ['in', 'out']);

        // Tanggal transaksi (bisa beda dari created_at, misal input mundur)
        $table->date('transaction_date');

        // Nomor referensi (opsional, misal nomor PO/invoice)
        $table->string('reference_number')->nullable();

        // Relasi ke product (wajib)
        $table->foreignId('product_id')
            ->constrained('products')
            ->cascadeOnUpdate()
            ->restrictOnDelete();

        // Relasi ke supplier (wajib kalau type=in, null kalau type=out)
        $table->foreignId('supplier_id')
            ->nullable()
            ->constrained('suppliers')
            ->cascadeOnUpdate()
            ->restrictOnDelete();

        // Relasi ke buyer (wajib kalau type=out, null kalau type=in)
        $table->foreignId('buyer_id')
            ->nullable()
            ->constrained('buyers')
            ->cascadeOnUpdate()
            ->restrictOnDelete();

        // Quantity dalam kg (selalu disimpan dalam kg, walaupun input pakai karung)
        $table->decimal('quantity_kg', 12, 2);

        // Catatan input asli: berapa unit & satuannya (untuk display kembali)
        $table->decimal('input_quantity', 12, 2);
        $table->enum('input_unit', ['kg', 'karung']);

        // Harga per kg (opsional, untuk nota)
        $table->decimal('price_per_kg', 12, 2)->nullable();

        // Total transaksi (opsional)
        $table->decimal('total_price', 14, 2)->nullable();

        // Catatan tambahan
        $table->text('notes')->nullable();

        // Siapa yang input (untuk audit)
        $table->foreignId('user_id')
            ->nullable()
            ->constrained('users')
            ->cascadeOnUpdate()
            ->nullOnDelete();

        $table->timestamps();

        // Index untuk query stok cepat
        $table->index(['product_id', 'type']);
        $table->index('transaction_date');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_transactions');
    }
};
