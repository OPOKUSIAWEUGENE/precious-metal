<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FeeSeeder extends Seeder
{
    public function run(): void
    {
        $adminId = DB::table('users')->where('email', 'admin@gmail.com')->value('id');

        DB::table('fees')->updateOrInsert(
            ['status' => 'active'],
            [
                'id' => DB::table('fees')->where('status', 'active')->value('id') ?? (string) Str::orderedUuid(),
                'fee' => 12.5000,
                'effective_at' => now(),
                'notes' => 'Seeded service fee',
                'added_by' => $adminId,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
