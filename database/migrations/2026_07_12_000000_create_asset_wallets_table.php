<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('asset_wallets', function (Blueprint $table) {
            $table->id();

            $table->string('owner_type')->nullable();
            $table->uuid('owner_id')->nullable();

            $table->foreignUuid('user_id')
                ->nullable()
                ->constrained('users')
                ->cascadeOnDelete();

            $table->uuid('wallet_reference')->unique();

            $table->decimal('available_weight', 20, 8)->default(0);
            $table->decimal('reserved_weight', 20, 8)->default(0);

            $table->string('weight_unit', 20)->default('gram');

            // 0.9999 represents 99.99% purity
            $table->decimal('purity', 8, 6)->default(0.999900);

            $table->string('status', 30)->default('active');

            $table->timestamp('last_transaction_at')->nullable();

            $table->timestamps();

            $table->unique(['owner_type', 'owner_id', 'weight_unit', 'purity']);
            $table->index('status');
            $table->index('user_id');
            $table->index(['owner_type', 'owner_id']);
        });

        $now = now();
        $existingUsers = DB::table('users')->select('id')->get();

        foreach ($existingUsers as $user) {
            DB::table('asset_wallets')->insertOrIgnore([
                'owner_type' => \App\Modules\v0\Users\Models\User::class,
                'owner_id' => $user->id,
                'user_id' => $user->id,
                'wallet_reference' => (string) \Illuminate\Support\Str::uuid(),
                'available_weight' => 0,
                'reserved_weight' => 0,
                'weight_unit' => 'gram',
                'purity' => '0.999900',
                'status' => 'active',
                'last_transaction_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_wallets');
    }
};
