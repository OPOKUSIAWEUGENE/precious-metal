<?php

namespace Database\Seeders;

use App\Modules\v0\Users\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create Platform Manager Admin
        User::firstOrCreate(
            ['email' => 'admin@neutek.local'],
            [
                'id' => (string) \Illuminate\Support\Str::orderedUuid(),
                'password' => 'password123',
                'first_name' => 'System',
                'last_name' => 'Admin',
                'phone_number' => '+233200000000',
                'role' => 'admin',
                'change_password' => false,
                'active' => true,
            ]
        );

        // Create Platform Manager Staff
        User::firstOrCreate(
            ['email' => 'staff@neutek.local'],
            [
                'id' => (string) \Illuminate\Support\Str::orderedUuid(),
                'password' => 'password123',
                'first_name' => 'Support',
                'last_name' => 'Staff',
                'phone_number' => '+233200000001',
                'role' => 'staff',
                'change_password' => false,
                'active' => true,
            ]
        );

        $this->command->info('Users seeded successfully! Credentials: admin@neutek.local / password123');
    }
}
