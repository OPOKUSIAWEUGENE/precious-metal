<?php

namespace Database\Seeders;

use App\Modules\v0\Users\Models\User;
use App\Modules\v0\Users\Models\Role;
use App\Modules\v0\Users\Models\Profile;
use App\Modules\v0\Users\Enums\ProfileType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles
        $roles = [
            ['name' => 'admin', 'description' => 'System Administrator'],
            ['name' => 'operator_admin', 'description' => 'Operator Administrator'],
            ['name' => 'driver', 'description' => 'Vehicle Driver'],
            ['name' => 'passenger', 'description' => 'Passenger/User'],
            ['name' => 'platform_manager', 'description' => 'Platform Manager'],
        ];

        foreach ($roles as $roleData) {
            Role::firstOrCreate(['name' => $roleData['name']], $roleData);
        }

        // Create admin user
        $adminRole = Role::where('name', 'admin')->first();
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@rushour.com'],
            [
                'password' => Hash::make('password123'),
                'first_name' => 'System',
                'last_name' => 'Admin',
                'phone_number' => '+233200000000',
                'active' => true,
            ]
        );
        $adminUser->profiles()->firstOrCreate(
            ['user_id' => $adminUser->id],
            [
                'profile_type' => ProfileType::PLATFORM_OPERATOR,
                'role_id' => $adminRole->id,
                'active' => true,
                'current' => true,
            ]
        );

        // Create test operator user
        $operatorRole = Role::where('name', 'operator_admin')->first();
        $operatorUser = User::firstOrCreate(
            ['email' => 'operator@rushour.com'],
            [
                'password' => Hash::make('password123'),
                'first_name' => 'Test',
                'last_name' => 'Operator',
                'phone_number' => '+233200000001',
                'active' => true,
            ]
        );
        $operatorUser->profiles()->firstOrCreate(
            ['user_id' => $operatorUser->id],
            [
                'profile_type' => ProfileType::CLIENT_OPERATOR,
                'role_id' => $operatorRole->id,
                'active' => true,
                'current' => true,
            ]
        );

        // Create test driver user
        $driverRole = Role::where('name', 'driver')->first();
        $driverUser = User::firstOrCreate(
            ['email' => 'driver@rushour.com'],
            [
                'password' => Hash::make('password123'),
                'first_name' => 'Test',
                'last_name' => 'Driver',
                'phone_number' => '+233200000002',
                'active' => true,
            ]
        );
        $driverUser->profiles()->firstOrCreate(
            ['user_id' => $driverUser->id],
            [
                'profile_type' => ProfileType::DRIVER,
                'role_id' => $driverRole->id,
                'active' => true,
                'current' => true,
                'license' => 'DL-123456789',
            ]
        );

        // Create test passenger user
        $passengerRole = Role::where('name', 'passenger')->first();
        $passengerUser = User::firstOrCreate(
            ['email' => 'passenger@rushour.com'],
            [
                'password' => Hash::make('password123'),
                'first_name' => 'Test',
                'last_name' => 'Passenger',
                'phone_number' => '+233200000003',
                'active' => true,
            ]
        );
        $passengerUser->profiles()->firstOrCreate(
            ['user_id' => $passengerUser->id],
            [
                'profile_type' => ProfileType::PASSENGER,
                'role_id' => $passengerRole->id,
                'active' => true,
                'current' => true,
            ]
        );

        $this->command->info('Users and Roles seeded successfully!');
    }
}