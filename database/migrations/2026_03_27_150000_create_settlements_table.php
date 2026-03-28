<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settlements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('operator_id')->nullable();
            $table->uuid('driver_profile_id')->nullable();
            $table->date('period_start');
            $table->date('period_end');
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->decimal('commission_amount', 10, 2)->default(0);
            $table->decimal('net_amount', 10, 2)->default(0);
            $table->string('status')->default('PENDING');
            $table->string('payment_method')->nullable();
            $table->string('payment_reference')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('operator_id')
                ->references('id')
                ->on('operators')
                ->onDelete('set null');

            $table->foreign('driver_profile_id')
                ->references('id')
                ->on('profiles')
                ->onDelete('set null');

            $table->index('status');
            $table->index(['operator_id', 'period_start', 'period_end']);
            $table->index(['driver_profile_id', 'period_start', 'period_end']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settlements');
    }
};