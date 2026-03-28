<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('trip_id')->nullable();
            $table->uuid('passenger_profile_id')->nullable();
            $table->integer('seat_count')->default(1);
            $table->string('status')->default('PENDING');
            $table->string('pickup_location')->nullable();
            $table->string('dropoff_location')->nullable();
            $table->decimal('total_price', 10, 2)->nullable();
            $table->timestamps();

            $table->foreign('trip_id')
                ->references('id')
                ->on('trips')
                ->onDelete('set null');

            $table->foreign('passenger_profile_id')
                ->references('id')
                ->on('profiles')
                ->onDelete('set null');

            $table->index('status');
            $table->index(['trip_id', 'status']);
            $table->index(['passenger_profile_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};