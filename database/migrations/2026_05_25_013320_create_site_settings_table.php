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
    Schema::create('site_settings', function (Blueprint $table) {
        $table->id();
        $table->string('key')->unique(); // identifier: 'hero_title', 'contact_email', dll
        $table->text('value')->nullable();
        $table->string('group')->default('general'); // pengelompokan: general, contact, hero, social
        $table->string('label'); // label yang ditampilkan di admin
        $table->enum('type', ['text', 'textarea', 'image', 'url', 'email', 'phone'])->default('text');
        $table->text('description')->nullable(); // hint untuk admin
        $table->integer('order')->default(0);
        $table->timestamps();

        $table->index('group');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
