<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customerId = DB::table('customers')->where('email', 'customer@gmail.com')->value('id') ?? (string) Str::orderedUuid();

        DB::table('customers')->updateOrInsert(
            ['email' => 'customer@gmail.com'],
            [
                'id' => $customerId,
                'password' => bcrypt('12345'),
                'first_name' => 'John',
                'last_name' => 'Customer',
                'phone_number' => '+233240000000',
                'date_of_birth' => '1990-01-01',
                'nationality' => 'Ghanaian',
                'resident_address' => '123 Tech Street, Accra',
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $this->command->info('Customers seeded successfully! Credentials: customer@gmail.com / 12345');
    }
}
