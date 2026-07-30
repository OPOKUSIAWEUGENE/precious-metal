<?php

namespace Database\Seeders;

use App\Modules\v0\Customers\Models\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WithdrawalSeeder extends Seeder
{
    public function run(): void
    {
        $customerId = DB::table('customers')->where('email', 'customer@gmail.com')->value('id');
        $customerWalletId = DB::table('asset_wallets')
            ->where('owner_type', Customer::class)
            ->where('owner_id', $customerId)
            ->value('id');
        $buyingRateId = DB::table('rates')
            ->where('rate_type', 'buying')
            ->where('status', 'active')
            ->value('id');

        if (!$customerWalletId || !$buyingRateId) {
            return;
        }

        DB::table('withdrawals')->updateOrInsert(
            [
                'asset_wallet_id' => $customerWalletId,
                'owner_type' => Customer::class,
                'owner_id' => $customerId,
                'rate_id' => $buyingRateId,
                'conversion_type' => 'gold_to_cash',
            ],
            [
                'id' => DB::table('withdrawals')
                    ->where('asset_wallet_id', $customerWalletId)
                    ->where('owner_type', Customer::class)
                    ->where('owner_id', $customerId)
                    ->where('rate_id', $buyingRateId)
                    ->value('id') ?? (string) Str::orderedUuid(),
                'requested_amount' => 2.00000000,
                'asset_amount' => 2.00000000,
                'cash_amount' => 2500.00,
                'conversion_rate' => 1250.0000,
                'status' => 'pending',
                'notes' => 'Seeded withdrawal',
                'approved_by' => null,
                'approved_at' => null,
                'rejected_by' => null,
                'rejected_at' => null,
                'rejection_reason' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
