<?php

namespace Database\Seeders;

use App\Modules\v0\Operators\Models\Operator;
use App\Modules\v0\Operators\Enums\OperatorType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OperatorSeeder extends Seeder
{
    public function run(): void
    {
        $operators = [
            [
                'id' => Str::orderedUuid()->toString(),
                'name' => 'RushHour Platform',
                'name_alias' => 'RHP',
                'operator_type' => OperatorType::PLATFORM->value,
                'contact_email' => 'info@rushour.com',
                'contact_phone' => '+233300000000',
                'profile' => 'Platform operator for RushHour',
                'active' => true,
            ],
            [
                'id' => Str::orderedUuid()->toString(),
                'name' => 'Accra Metro Transport',
                'name_alias' => 'AMT',
                'operator_type' => OperatorType::CLIENT->value,
                'contact_email' => 'info@accrametro.com',
                'contact_phone' => '+233300000001',
                'profile' => 'Accra Metropolitan Transport Service',
                'active' => true,
            ],
            [
                'id' => Str::orderedUuid()->toString(),
                'name' => 'Tema Express',
                'name_alias' => 'TEX',
                'operator_type' => OperatorType::CLIENT->value,
                'contact_email' => 'info@temaexpress.com',
                'contact_phone' => '+233300000002',
                'profile' => 'Tema Express Transport Service',
                'active' => true,
            ],
            [
                'id' => Str::orderedUuid()->toString(),
                'name' => 'Kumasi Connect',
                'name_alias' => 'KC',
                'operator_type' => OperatorType::CLIENT->value,
                'contact_email' => 'info@kumasiconnect.com',
                'contact_phone' => '+233300000003',
                'profile' => 'Kumasi Connect Transport Service',
                'active' => true,
            ],
        ];

        foreach ($operators as $operatorData) {
            $existing = Operator::where('name', $operatorData['name'])->first();
            
            if ($existing) {
                $existing->update($operatorData);
            } else {
                Operator::create($operatorData);
            }
        }

        $this->command->info('Operators seeded successfully!');
    }
}