<?php

namespace Database\Seeders;

use App\Modules\v0\User\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            // Platform Operator roles
            [
                'name' => 'platform_admin',
                'description' => 'Platform administrator with full system access',
            ],
            [
                'name' => 'platform_manager',
                'description' => 'Platform manager with operational access',
            ],
            [
                'name' => 'platform_finance',
                'description' => 'Platform finance personnel with financial transaction access',
            ],
            [
                'name' => 'platform_support',
                'description' => 'Platform support personnel',
            ],
            // Client Operator roles
            [
                'name' => 'client_admin',
                'description' => 'Client organization administrator with full access to their organization',
            ],
            [
                'name' => 'client_manager',
                'description' => 'Client organization manager with operational access',
            ],
            [
                'name' => 'client_finance',
                'description' => 'Client organization finance personnel',
            ],
            [
                'name' => 'client_staff',
                'description' => 'Client organization staff member',
            ],
            // Driver roles
            [
                'name' => 'driver',
                'description' => 'Driver with vehicle operation access',
            ],
            [
                'name' => 'driver_supervisor',
                'description' => 'Driver supervisor with driver management access',
            ],
            // Passenger roles
            [
                'name' => 'passenger',
                'description' => 'Regular passenger user',
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['name' => $role['name']],
                $role
            );
        }
    }
}