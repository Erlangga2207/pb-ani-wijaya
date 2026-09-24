<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->date('adjustment_date');
            $table->foreignId('item_id')->constrained('items')->restrictOnDelete();
            $table->decimal('system_kg', 12, 2);
            $table->decimal('physical_kg', 12, 2);
            $table->decimal('difference_kg', 12, 2);
            $table->text('reason');
            $table->string('status')->default('posted');
            $table->text('void_reason')->nullable();
            $table->timestamp('voided_at')->nullable();
            $table->foreignId('voided_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('adjustment_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_adjustments');
    }
};
