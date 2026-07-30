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
        if (Schema::hasTable('withdrawals')) {
            return;
        }

        Schema::create('withdrawals', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignId('asset_wallet_id')
                ->constrained('asset_wallets')
                ->cascadeOnDelete();

            $table->string('owner_type');
            $table->uuid('owner_id');

            $table->foreignUuid('rate_id')
                ->nullable()
                ->constrained('rates')
                ->nullOnDelete();

            $table->decimal('requested_amount', 20, 8);
            $table->decimal('asset_amount', 20, 8);
            $table->decimal('cash_amount', 20, 2);
            $table->decimal('conversion_rate', 10, 4);
            $table->string('conversion_type', 30)->default('gold_to_cash');
            $table->string('status', 30)->default('pending');
            $table->text('notes')->nullable();

            $table->foreignUuid('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('approved_at')->nullable();

            $table->foreignUuid('rejected_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('rejected_at')->nullable();
            $table->text('rejection_reason')->nullable();

            $table->timestamps();

            $table->index('status');
            $table->index('asset_wallet_id');
            $table->index('rate_id');
            $table->index(['owner_type', 'owner_id'], 'withdrawals_owner_lookup_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('withdrawals');
    }
};
