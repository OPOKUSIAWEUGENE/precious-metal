<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trips', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('route_id')->nullable();
            $table->uuid('vehicle_id')->nullable();
            $table->uuid('driver_profile_id')->nullable();
            $table->uuid('operator_id')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->uuid('departure_stop_id')->nullable();
            $table->uuid('arrival_stop_id')->nullable();
            $table->string('status')->default('SCHEDULED');
            $table->decimal('price', 10, 2)->nullable();
            $table->integer('available_seats')->default(0);
            $table->integer('booked_seats')->default(0);
            $table->timestamps();

            $table->foreign('route_id')
                ->references('id')
                ->on('routes')
                ->onDelete('set null');

            $table->foreign('vehicle_id')
                ->references('id')
                ->on('vehicles')
                ->onDelete('set null');

            $table->foreign('driver_profile_id')
                ->references('id')
                ->on('profiles')
                ->onDelete('set null');

            $table->foreign('operator_id')
                ->references('id')
                ->on('operators')
                ->onDelete('set null');

            $table->foreign('departure_stop_id')
                ->references('id')
                ->on('route_stops')
                ->onDelete('set null');

            $table->foreign('arrival_stop_id')
                ->references('id')
                ->on('route_stops')
                ->onDelete('set null');

            $table->index('status');
            $table->index('scheduled_at');
            $table->index(['operator_id', 'scheduled_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};