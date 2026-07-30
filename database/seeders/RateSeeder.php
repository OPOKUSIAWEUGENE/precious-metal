<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RateSeeder extends Seeder
{
    public function run(): void
    {
        $adminId = DB::table('users')->where('email', 'admin@gmail.com')->value('id');

        $rates = [
            [
                'rate_type' => 'buying',
                'rate' => 1250.0000,
                'notes' => 'Seeded buying rate',
            ],
            [
                'rate_type' => 'selling',
                'rate' => 1180.0000,
                'notes' => 'Seeded selling rate',
            ],
        ];

        foreach ($rates as $rate) {
            DB::table('rates')->updateOrInsert(
                ['rate_type' => $rate['rate_type'], 'status' => 'active'],
                [
                    'id' => DB::table('rates')->where('rate_type', $rate['rate_type'])->where('status', 'active')->value('id') ?? (string) Str::orderedUuid(),
                    'rate' => $rate['rate'],
                    'effective_at' => now(),
                    'notes' => $rate['notes'],
                    'added_by' => $adminId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
