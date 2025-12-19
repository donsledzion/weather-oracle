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
        Schema::create('notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->string('email')->nullable()->index();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('token')->unique(); // Unikalny token do zarządzania powiadomieniami
            $table->boolean('first_snapshot_enabled')->default(true);
            $table->boolean('daily_summary_enabled')->default(true);
            $table->boolean('final_summary_enabled')->default(true);
            $table->timestamps();

            // Jeden rekord per email LUB user_id
            $table->unique(['email'], 'unique_email');
            $table->unique(['user_id'], 'unique_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_preferences');
    }
};
