<?php

namespace Database\Seeders;

use App\Modules\v0\Customers\Models\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        // Create Test Customer
        Customer::firstOrCreate(
            ['email' => 'customer@neutek.local'],
            [
                'password' => Hash::make('password123'),
                'first_name' => 'John',
                'last_name' => 'Customer',
                'phone_number' => '+233240000000',
                'date_of_birth' => '1990-01-01',
                'nationality' => 'Ghanaian',
                'resident_address' => '123 Tech Street, Accra',
                'active' => true,
            ]
        );

        $this->command->info('Customers seeded successfully! Credentials: customer@neutek.local / password123');
    }
}
