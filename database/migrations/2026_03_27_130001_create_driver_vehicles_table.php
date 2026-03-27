<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('driver_vehicles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('profile_id');
            $table->uuid('vehicle_id');
            $table->timestamps();

            $table->foreign('profile_id')->references('id')->on('profiles')->onDelete('cascade');
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('cascade');
            $table->unique(['profile_id', 'vehicle_id']);
            $table->index('profile_id');
            $table->index('vehicle_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('driver_vehicles');
    }
};