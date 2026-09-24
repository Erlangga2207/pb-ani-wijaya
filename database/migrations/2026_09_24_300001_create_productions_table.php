<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('productions', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->date('production_date');
            $table->foreignId('input_item_id')->constrained('items')->restrictOnDelete();
            $table->decimal('input_kg', 12, 2);
            $table->decimal('input_cost_per_kg', 15, 2); // snapshot avg cost gabah
            $table->decimal('input_total_cost', 15, 2);
            $table->decimal('main_output_kg', 12, 2); // kg beras
            $table->decimal('yield_percent', 5, 2); // rendemen
            $table->decimal('shrinkage_kg', 12, 2); // susut
            $table->unsignedInteger('machine_downtime_minutes')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->default('posted');
            $table->text('void_reason')->nullable();
            $table->timestamp('voided_at')->nullable();
            $table->foreignId('voided_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('production_date');
        });

        Schema::create('production_outputs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_id')->constrained('productions')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->restrictOnDelete();
            $table->decimal('quantity_kg', 12, 2);
            $table->decimal('unit_cost', 15, 2);

            $table->unique(['production_id', 'item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production_outputs');
        Schema::dropIfExists('productions');
    }
};
