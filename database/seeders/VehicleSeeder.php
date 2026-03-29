<?php

namespace Database\Seeders;

use App\Modules\v0\Fleets\Models\Vehicle;
use App\Modules\v0\Fleets\Enums\VehicleStatus;
use App\Modules\v0\Operators\Models\Operator;
use App\Modules\v0\Operators\Enums\OperatorType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class VehicleSeeder extends Seeder
{
    public function run(): void
    {
        $operators = Operator::where('operator_type', OperatorType::CLIENT->value)->get();

        if ($operators->isEmpty()) {
            $this->command->warn('No operators found. Please run OperatorSeeder first.');
            return;
        }

        $platformOperator = Operator::where('operator_type', OperatorType::PLATFORM->value)->first();
        $createdBy = $platformOperator?->id;

        $vehicles = [
            [
                'id' => Str::orderedUuid()->toString(),
                'fleet_identifier' => 'AMT-BUS-001',
                'type' => 'BUS',
                'plate_number' => 'GW-1234-20',
                'capacity' => 25,
                'status' => VehicleStatus::ACTIVE,
                'operator_id' => $operators->first()->id,
                'created_by' => $createdBy,
                'updated_by' => $createdBy,
            ],
            [
                'id' => Str::orderedUuid()->toString(),
                'fleet_identifier' => 'AMT-BUS-002',
                'type' => 'BUS',
                'plate_number' => 'GW-1235-20',
                'capacity' => 25,
                'status' => VehicleStatus::ACTIVE,
                'operator_id' => $operators->first()->id,
                'created_by' => $createdBy,
                'updated_by' => $createdBy,
            ],
            [
                'id' => Str::orderedUuid()->toString(),
                'fleet_identifier' => 'TEX-BUS-001',
                'type' => 'BUS',
                'plate_number' => 'GW-2234-21',
                'capacity' => 30,
                'status' => VehicleStatus::ACTIVE,
                'operator_id' => $operators->skip(1)->first()?->id ?? $operators->first()->id,
                'created_by' => $createdBy,
                'updated_by' => $createdBy,
            ],
            [
                'id' => Str::orderedUuid()->toString(),
                'fleet_identifier' => 'TEX-BUS-002',
                'type' => 'BUS',
                'plate_number' => 'GW-2235-21',
                'capacity' => 30,
                'status' => VehicleStatus::ACTIVE,
                'operator_id' => $operators->skip(1)->first()?->id ?? $operators->first()->id,
                'created_by' => $createdBy,
                'updated_by' => $createdBy,
            ],
            [
                'id' => Str::orderedUuid()->toString(),
                'fleet_identifier' => 'KC-BUS-001',
                'type' => 'BUS',
                'plate_number' => 'GW-3234-22',
                'capacity' => 35,
                'status' => VehicleStatus::ACTIVE,
                'operator_id' => $operators->skip(2)->first()?->id ?? $operators->first()->id,
                'created_by' => $createdBy,
                'updated_by' => $createdBy,
            ],
        ];

        foreach ($vehicles as $vehicleData) {
            Vehicle::firstOrCreate(
                ['plate_number' => $vehicleData['plate_number']],
                $vehicleData
            );
        }

        $this->command->info('Vehicles seeded successfully!');
    }
}