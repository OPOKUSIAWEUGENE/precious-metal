<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
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
            [
                'name' => 'driver',
                'description' => 'Driver with vehicle operation access',
            ],
            [
                'name' => 'driver_supervisor',
                'description' => 'Driver supervisor with driver management access',
            ],
            [
                'name' => 'passenger',
                'description' => 'Regular passenger user',
            ],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['name' => $role['name']],
                [
                    'id' => DB::table('roles')->where('name', $role['name'])->value('id') ?? (string) Str::orderedUuid(),
                    'description' => $role['description'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
