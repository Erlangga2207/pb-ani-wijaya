<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contact_messages', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->string('company')->nullable()->after('phone');
            $table->string('country')->nullable()->after('company');
            $table->string('status')->default('new')->after('message'); // App\Enums\LeadStatus
            $table->foreignId('buyer_id')->nullable()->after('status')->constrained('buyers')->nullOnDelete();
            $table->foreignId('handled_by')->nullable()->after('buyer_id')->constrained('users')->nullOnDelete();
            $table->string('ip_address', 45)->nullable()->after('handled_by');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::table('contact_messages', function (Blueprint $table) {
            $table->dropConstrainedForeignId('buyer_id');
            $table->dropConstrainedForeignId('handled_by');
            $table->dropIndex(['status']);
            $table->dropColumn(['phone', 'company', 'country', 'status', 'ip_address']);
        });
    }
};
