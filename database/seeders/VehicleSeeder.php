<?php

namespace Database\Seeders;

use App\Modules\v0\Fleets\Models\Vehicle;
use App\Modules\v0\Fleets\Enums\VehicleStatus;
use App\Modules\v0\Operators\Models\Operator;
use Illuminate\Database\Seeder;

class VehicleSeeder extends Seeder
{
    public function run(): void
    {
        $operators = Operator::where('operator_type', 'CLIENT')->get();

        if ($operators->isEmpty()) {
            $this->command->warn('No operators found. Please run OperatorSeeder first.');
            return;
        }

        $vehicles = [
            [
                'registration_number' => 'GW-1234-20',
                'model' => 'Toyota Hiace',
                'make' => 'Toyota',
                'year' => 2020,
                'capacity' => 25,
                'fleet_identifier' => 'AMT-BUS-001',
                'status' => VehicleStatus::ACTIVE,
                'operator_id' => $operators->first()->id,
            ],
            [
                'registration_number' => 'GW-1235-20',
                'model' => 'Toyota Hiace',
                'make' => 'Toyota',
                'year' => 2020,
                'capacity' => 25,
                'fleet_identifier' => 'AMT-BUS-002',
                'status' => VehicleStatus::ACTIVE,
                'operator_id' => $operators->first()->id,
            ],
            [
                'registration_number' => 'GW-2234-21',
                'model' => 'Isuzu Bus',
                'make' => 'Isuzu',
                'year' => 2021,
                'capacity' => 30,
                'fleet_identifier' => 'TEX-BUS-001',
                'status' => VehicleStatus::ACTIVE,
                'operator_id' => $operators->skip(1)->first()?->id ?? $operators->first()->id,
            ],
            [
                'registration_number' => 'GW-2235-21',
                'model' => 'Isuzu Bus',
                'make' => 'Isuzu',
                'year' => 2021,
                'capacity' => 30,
                'fleet_identifier' => 'TEX-BUS-002',
                'status' => VehicleStatus::ACTIVE,
                'operator_id' => $operators->skip(1)->first()?->id ?? $operators->first()->id,
            ],
            [
                'registration_number' => 'GW-3234-22',
                'model' => 'Hino Bus',
                'make' => 'Hino',
                'year' => 2022,
                'capacity' => 35,
                'fleet_identifier' => 'KC-BUS-001',
                'status' => VehicleStatus::ACTIVE,
                'operator_id' => $operators->skip(2)->first()?->id ?? $operators->first()->id,
            ],
        ];

        foreach ($vehicles as $vehicleData) {
            Vehicle::firstOrCreate(
                ['registration_number' => $vehicleData['registration_number']],
                $vehicleData
            );
        }

        $this->command->info('Vehicles seeded successfully!');
    }
}