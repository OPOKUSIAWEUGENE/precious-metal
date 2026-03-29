<?php

namespace Database\Seeders;

use App\Modules\v0\Routes\Models\Route;
use App\Modules\v0\Routes\Models\RouteStop;
use App\Modules\v0\Routes\Enums\RouteStatus;
use App\Modules\v0\Operators\Models\Operator;
use App\Modules\v0\Operators\Enums\OperatorType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RouteSeeder extends Seeder
{
    public function run(): void
    {
        $operators = Operator::where('operator_type', OperatorType::CLIENT->value)->get();

        if ($operators->isEmpty()) {
            $this->command->warn('No operators found. Please run OperatorSeeder first.');
            return;
        }

        // Create route stops
        $stops = [
            ['name' => 'Accra Central Station', 'latitude' => 5.6037, 'longitude' => -0.1870, 'description' => 'Main station in Accra'],
            ['name' => 'Kumasi Central Station', 'latitude' => 6.6885, 'longitude' => -1.6244, 'description' => 'Main station in Kumasi'],
            ['name' => 'Tema Station', 'latitude' => 5.6297, 'longitude' => -0.0019, 'description' => 'Main station in Tema'],
            ['name' => 'Kumasi Mall', 'latitude' => 6.6969, 'longitude' => -1.6225, 'description' => 'Shopping mall in Kumasi'],
            ['name' => 'Accra Mall', 'latitude' => 5.6357, 'longitude' => -0.1760, 'description' => 'Shopping mall in Accra'],
            ['name' => 'Takoradi Station', 'latitude' => 4.9035, 'longitude' => -1.7722, 'description' => 'Main station in Takoradi'],
            ['name' => 'Cape Coast Station', 'latitude' => 5.1054, 'longitude' => -1.2546, 'description' => 'Main station in Cape Coast'],
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
        if (count($createdStops) >= 2) {
            $routes = [
                [
                    'id' => Str::orderedUuid()->toString(),
                    'name' => 'Accra - Kumasi',
                    'start_stop_id' => $createdStops[0]->id,
                    'end_stop_id' => $createdStops[1]->id,
                    'status' => RouteStatus::ACTIVE,
                    'route_type' => 'INTERCITY',
                ],
                [
                    'id' => Str::orderedUuid()->toString(),
                    'name' => 'Accra - Tema',
                    'start_stop_id' => $createdStops[0]->id,
                    'end_stop_id' => $createdStops[2]->id,
                    'status' => RouteStatus::ACTIVE,
                    'route_type' => 'INTRACITY',
                ],
                [
                    'id' => Str::orderedUuid()->toString(),
                    'name' => 'Kumasi - Takoradi',
                    'start_stop_id' => $createdStops[1]->id,
                    'end_stop_id' => $createdStops[5]->id,
                    'status' => RouteStatus::ACTIVE,
                    'route_type' => 'INTERCITY',
                ],
                [
                    'id' => Str::orderedUuid()->toString(),
                    'name' => 'Accra - Cape Coast',
                    'start_stop_id' => $createdStops[0]->id,
                    'end_stop_id' => $createdStops[6]->id,
                    'status' => RouteStatus::ACTIVE,
                    'route_type' => 'INTERCITY',
                ],
            ];

            foreach ($routes as $routeData) {
                Route::firstOrCreate(
                    ['name' => $routeData['name']],
                    $routeData
                );
            }
        }

        $this->command->info('Routes and Stops seeded successfully!');
    }
}