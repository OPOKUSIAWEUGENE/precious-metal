<?php

namespace Database\Seeders;

use App\Modules\v0\Routes\Models\Route;
use App\Modules\v0\Routes\Models\RouteStop;
use App\Modules\v0\Routes\Enums\RouteStatus;
use App\Modules\v0\Operators\Models\Operator;
use Illuminate\Database\Seeder;

class RouteSeeder extends Seeder
{
    public function run(): void
    {
        $operators = Operator::where('type', 'CLIENT')->get();

        if ($operators->isEmpty()) {
            $this->command->warn('No operators found. Please run OperatorSeeder first.');
            return;
        }

        // Create route stops
        $stops = [
            ['name' => 'Accra Central Station', 'latitude' => 5.6037, 'longitude' => -0.1870, 'city' => 'Accra'],
            ['name' => 'Kumasi Central Station', 'latitude' => 6.6885, 'longitude' => -1.6244, 'city' => 'Kumasi'],
            ['name' => 'Tema Station', 'latitude' => 5.6297, 'longitude' => -0.0019, 'city' => 'Tema'],
            ['name' => 'Kumasi Mall', 'latitude' => 6.6969, 'longitude' => -1.6225, 'city' => 'Kumasi'],
            ['name' => 'Accra Mall', 'latitude' => 5.6357, 'longitude' => -0.1760, 'city' => 'Accra'],
            ['name' => 'Takoradi Station', 'latitude' => 4.9035, 'longitude' => -1.7722, 'city' => 'Takoradi'],
            ['name' => 'Cape Coast Station', 'latitude' => 5.1054, 'longitude' => -1.2546, 'city' => 'Cape Coast'],
        ];

        $createdStops = [];
        foreach ($stops as $stopData) {
            $stop = RouteStop::firstOrCreate(
                ['name' => $stopData['name']],
                $stopData
            );
            $createdStops[] = $stop;
        }

        // Create routes
        $routes = [
            [
                'name' => 'Accra - Kumasi Express',
                'code' => 'AK001',
                'description' => 'Direct express service from Accra to Kumasi',
                'distance_km' => 270,
                'estimated_duration_minutes' => 240,
                'base_price' => 50.00,
                'status' => RouteStatus::ACTIVE,
                'operator_id' => $operators->first()->id,
            ],
            [
                'name' => 'Accra - Tema',
                'code' => 'AT001',
                'description' => 'Accra to Tema city service',
                'distance_km' => 30,
                'estimated_duration_minutes' => 45,
                'base_price' => 10.00,
                'status' => RouteStatus::ACTIVE,
                'operator_id' => $operators->first()->id,
            ],
            [
                'name' => 'Kumasi - Takoradi',
                'code' => 'KT001',
                'description' => 'Kumasi to Takoradi coastal route',
                'distance_km' => 220,
                'estimated_duration_minutes' => 180,
                'base_price' => 40.00,
                'status' => RouteStatus::ACTIVE,
                'operator_id' => $operators->skip(1)->first()?->id ?? $operators->first()->id,
            ],
            [
                'name' => 'Accra - Cape Coast',
                'code' => 'AC001',
                'description' => 'Accra to Cape Coast via coastal road',
                'distance_km' => 145,
                'estimated_duration_minutes' => 120,
                'base_price' => 25.00,
                'status' => RouteStatus::ACTIVE,
                'operator_id' => $operators->skip(2)->first()?->id ?? $operators->first()->id,
            ],
        ];

        foreach ($routes as $routeData) {
            Route::firstOrCreate(
                ['code' => $routeData['code']],
                $routeData
            );
        }

        $this->command->info('Routes and Stops seeded successfully!');
    }
}