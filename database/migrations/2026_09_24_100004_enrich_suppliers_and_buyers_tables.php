<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->string('village')->nullable()->after('address'); // desa
            $table->string('district')->nullable()->after('village'); // kecamatan
            $table->string('city')->nullable()->after('district');
            $table->index('village');
        });

        Schema::table('buyers', function (Blueprint $table) {
            // enum MySQL -> string + PHP enum App\Enums\BuyerType
            $table->string('type')->default('retail')->change();
            $table->string('email')->nullable()->after('phone');
            $table->string('city')->nullable()->after('address');
            $table->string('province')->nullable()->after('city');
            $table->string('country')->default('Indonesia')->after('province');
            $table->index('city');
        });
    }

    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropIndex(['village']);
            $table->dropColumn(['village', 'district', 'city']);
        });

        Schema::table('buyers', function (Blueprint $table) {
            $table->dropIndex(['city']);
            $table->dropColumn(['email', 'city', 'province', 'country']);
        });
    }
};
