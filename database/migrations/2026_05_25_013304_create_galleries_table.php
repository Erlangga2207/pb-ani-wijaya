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
    Schema::create('galleries', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->text('description')->nullable();
        $table->enum('category', ['origin', 'processing', 'quality', 'logistics', 'product', 'facility', 'other'])
            ->default('other');
        $table->string('file_path'); // path file di storage
        $table->enum('file_type', ['image', 'video'])->default('image');
        $table->integer('order')->default(0); // urutan tampil
        $table->boolean('is_featured')->default(false); // foto unggulan tampil di beranda
        $table->boolean('is_active')->default(true);
        $table->timestamps();

        $table->index(['category', 'is_active']);
        $table->index('is_featured');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('galleries');
    }
};
