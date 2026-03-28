<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commissions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('trip_id')->nullable();
            $table->uuid('operator_id')->nullable();
            $table->uuid('driver_profile_id')->nullable();
            $table->uuid('settlement_id')->nullable();
            $table->decimal('amount', 10, 2)->default(0);
            $table->decimal('rate', 5, 4)->default(0);
            $table->decimal('gross_amount', 10, 2)->default(0);
            $table->decimal('net_amount', 10, 2)->default(0);
            $table->string('status')->default('PENDING');
            $table->date('period_start')->nullable();
            $table->date('period_end')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('trip_id')
                ->references('id')
                ->on('trips')
                ->onDelete('set null');

            $table->foreign('operator_id')
                ->references('id')
                ->on('operators')
                ->onDelete('set null');

            $table->foreign('driver_profile_id')
                ->references('id')
                ->on('profiles')
                ->onDelete('set null');

            $table->foreign('settlement_id')
                ->references('id')
                ->on('settlements')
                ->onDelete('set null');

            $table->index('status');
            $table->index('trip_id');
            $table->index('settlement_id');
            $table->index(['operator_id', 'period_start', 'period_end']);
            $table->index(['driver_profile_id', 'period_start', 'period_end']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commissions');
    }
};