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
        Schema::create('public_monitor_locations', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // "Warsaw, Poland"
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->boolean('is_active')->default(true);
            $table->integer('max_concurrent_monitors')->default(3); // max active monitors simultaneously
            $table->integer('days_ahead')->default(10); // target_date = now + X days
            $table->integer('stagger_days')->default(3); // create new monitor every X days
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('public_monitor_locations');
    }
};
