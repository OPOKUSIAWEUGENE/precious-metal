<?php

namespace Database\Seeders;

use App\Modules\v0\Customers\Models\Customer;
use App\Modules\v0\Users\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AssetWalletSeeder extends Seeder
{
    public function run(): void
    {
        $adminId = DB::table('users')->where('email', 'admin@gmail.com')->value('id');
        $customerId = DB::table('customers')->where('email', 'customer@gmail.com')->value('id');

        $wallets = [
            [
                'owner_type' => User::class,
                'owner_id' => $adminId,
                'user_id' => $adminId,
                'available_weight' => 25.00000000,
                'reserved_weight' => 0,
            ],
            [
                'owner_type' => Customer::class,
                'owner_id' => $customerId,
                'user_id' => null,
                'available_weight' => 12.50000000,
                'reserved_weight' => 0,
            ],
        ];

        foreach ($wallets as $wallet) {
            DB::table('asset_wallets')->updateOrInsert(
                [
                    'owner_type' => $wallet['owner_type'],
                    'owner_id' => $wallet['owner_id'],
                    'weight_unit' => 'gram',
                    'purity' => '0.999900',
                ],
                [
                    'user_id' => $wallet['user_id'],
                    'wallet_reference' => DB::table('asset_wallets')
                        ->where('owner_type', $wallet['owner_type'])
                        ->where('owner_id', $wallet['owner_id'])
                        ->where('weight_unit', 'gram')
                        ->where('purity', '0.999900')
                        ->value('wallet_reference') ?? (string) Str::uuid(),
                    'available_weight' => $wallet['available_weight'],
                    'reserved_weight' => $wallet['reserved_weight'],
                    'status' => 'active',
                    'last_transaction_at' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
