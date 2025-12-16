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
        Schema::table('monitoring_requests', function (Blueprint $table) {
            // Verification and dashboard tokens
            $table->string('verification_token')->nullable()->unique()->after('status');
            $table->string('dashboard_token')->nullable()->index()->after('verification_token');
            $table->timestamp('expires_at')->nullable()->after('dashboard_token');

            // Update status column to accept new values
            // Note: existing records will keep 'active' status
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('monitoring_requests', function (Blueprint $table) {
            $table->dropColumn(['verification_token', 'dashboard_token', 'expires_at']);
        });
    }
};
