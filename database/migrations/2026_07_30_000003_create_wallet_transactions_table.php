<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('reference')->unique();
            $table->string('group_reference')->nullable();
            $table->string('operation', 30);
            $table->string('direction', 20)->default('debit');
            $table->string('status', 30)->default('pending');
            $table->foreignId('wallet_id')->constrained('asset_wallets')->cascadeOnDelete();
            $table->string('owner_type');
            $table->uuid('owner_id');
            $table->string('counterparty_type')->nullable();
            $table->uuid('counterparty_id')->nullable();
            $table->string('quote_reference')->nullable();
            $table->decimal('asset_amount', 20, 8)->default(0);
            $table->decimal('cash_amount', 20, 2)->nullable();
            $table->decimal('price_per_gram', 10, 4)->nullable();
            $table->decimal('fee_amount', 20, 2)->default(0);
            $table->decimal('total_amount', 20, 2)->nullable();
            $table->text('narration')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['owner_type', 'owner_id']);
            $table->index(['wallet_id', 'operation']);
            $table->index(['operation', 'status']);
            $table->index('reference');
            $table->index('group_reference');
            $table->index('quote_reference');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};
