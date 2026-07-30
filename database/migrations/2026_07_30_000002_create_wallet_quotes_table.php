<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallet_quotes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('reference')->unique();
            $table->string('operation', 30);
            $table->string('status', 30)->default('active');
            $table->string('currency', 10)->default('GHS');
            $table->decimal('cash_amount', 20, 2)->nullable();
            $table->decimal('gold_weight', 20, 8)->nullable();
            $table->decimal('price_per_gram', 10, 4)->nullable();
            $table->decimal('fee_rate', 10, 4)->nullable();
            $table->decimal('fee_amount', 20, 2)->default(0);
            $table->decimal('total_payable', 20, 2)->nullable();
            $table->decimal('total_receivable', 20, 2)->nullable();
            $table->string('recipient_type')->nullable();
            $table->uuid('recipient_id')->nullable();
            $table->unsignedBigInteger('source_wallet_id')->nullable();
            $table->unsignedBigInteger('destination_wallet_id')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->json('metadata')->nullable();
            $table->string('created_by_type')->nullable();
            $table->uuid('created_by_id')->nullable();
            $table->timestamps();

            $table->index(['operation', 'status']);
            $table->index('reference');
            $table->index('expires_at');
            $table->index(['created_by_type', 'created_by_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_quotes');
    }
};
