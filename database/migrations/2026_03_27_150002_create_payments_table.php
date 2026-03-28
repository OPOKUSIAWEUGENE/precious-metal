<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('booking_id')->nullable();
            $table->uuid('passenger_profile_id')->nullable();
            $table->uuid('operator_id')->nullable();
            $table->decimal('amount', 10, 2)->default(0);
            $table->string('currency', 3)->default('GHS');
            $table->string('payment_method')->nullable();
            $table->string('status')->default('PENDING');
            $table->string('transaction_id')->nullable()->unique();
            $table->string('external_reference')->nullable();
            $table->string('gateway', 100)->nullable();
            $table->json('gateway_response')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('booking_id')
                ->references('id')
                ->on('bookings')
                ->onDelete('set null');

            $table->foreign('passenger_profile_id')
                ->references('id')
                ->on('profiles')
                ->onDelete('set null');

            $table->foreign('operator_id')
                ->references('id')
                ->on('operators')
                ->onDelete('set null');

            $table->index('status');
            $table->index('booking_id');
            $table->index('passenger_profile_id');
            $table->index(['operator_id', 'created_at']);
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};