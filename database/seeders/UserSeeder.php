<?php

namespace Database\Seeders;

use App\Modules\v0\Users\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::withoutEvents(function (): void {
            // Create Platform Manager Admin
            $adminId = User::query()->where('email', 'admin@gmail.com')->value('id') ?? (string) Str::orderedUuid();

            DB::table('users')->updateOrInsert(
                ['email' => 'admin@gmail.com'],
                [
                    'id' => $adminId,
                    'password' => Hash::make('12345'),
                    'first_name' => 'System',
                    'last_name' => 'Admin',
                    'phone_number' => '+233200000000',
                    'role' => 'admin',
                    'active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            // Create Platform Manager Staff
            $staffId = User::query()->where('email', 'staff@neutek.local')->value('id') ?? (string) Str::orderedUuid();

            DB::table('users')->updateOrInsert(
                ['email' => 'staff@neutek.local'],
                [
                    'id' => $staffId,
                    'password' => Hash::make('password123'),
                    'first_name' => 'Support',
                    'last_name' => 'Staff',
                    'phone_number' => '+233200000001',
                    'role' => 'staff',
                    'active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        });

        $this->command->info('Users seeded successfully! Credentials: admin@gmail.com / 12345');
    }
}
