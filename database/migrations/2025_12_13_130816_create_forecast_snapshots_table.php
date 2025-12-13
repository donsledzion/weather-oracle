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
        Schema::create('forecast_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('monitoring_request_id')->constrained()->onDelete('cascade');
            $table->foreignId('weather_provider_id')->constrained()->onDelete('cascade');
            $table->json('forecast_data');
            $table->timestamp('fetched_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forecast_snapshots');
    }
};
