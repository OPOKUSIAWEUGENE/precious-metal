<?php

use App\Modules\v0\Users\Models\User;
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
        if (Schema::hasTable('gold_wallets') && ! Schema::hasTable('asset_wallets')) {
            Schema::rename('gold_wallets', 'asset_wallets');
        }

        if (Schema::hasTable('asset_wallets') && ! Schema::hasColumn('asset_wallets', 'owner_type')) {
            Schema::table('asset_wallets', function (Blueprint $table) {
                $table->string('owner_type')->nullable()->after('id');
                $table->uuid('owner_id')->nullable()->after('owner_type');
                $table->index(['owner_type', 'owner_id']);
            });

            DB::table('asset_wallets')
                ->whereNull('owner_type')
                ->update([
                    'owner_type' => User::class,
                    'owner_id' => DB::raw('user_id'),
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('asset_wallets') && ! Schema::hasTable('gold_wallets')) {
            Schema::rename('asset_wallets', 'gold_wallets');
        }
    }
};
