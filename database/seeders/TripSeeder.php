<?php

namespace Database\Seeders;

use App\Modules\v0\Trips\Models\Trip;
use App\Modules\v0\Trips\Models\Booking;
use App\Modules\v0\Trips\Enums\TripStatus;
use App\Modules\v0\Trips\Enums\BookingStatus;
use App\Modules\v0\Routes\Models\Route;
use App\Modules\v0\Fleets\Models\Vehicle;
use App\Modules\v0\Users\Models\Profile;
use App\Modules\v0\Users\Enums\ProfileType;
use Illuminate\Database\Seeder;

class TripSeeder extends Seeder
{
    public function run(): void
    {
        $routes = Route::where('status', 'ACTIVE')->get();
        $vehicles = Vehicle::where('status', 'ACTIVE')->get();
        $driverProfiles = Profile::where('profile_type', ProfileType::DRIVER)->where('active', true)->get();

        if ($routes->isEmpty() || $vehicles->isEmpty() || $driverProfiles->isEmpty()) {
            $this->command->warn('Please run RouteSeeder, VehicleSeeder, and UserSeeder first.');
            return;
        }

        // Create some sample trips
        $trips = [
            [
                'route_id' => $routes->first()->id,
                'vehicle_id' => $vehicles->first()->id,
                'driver_profile_id' => $driverProfiles->first()->id,
                'operator_id' => $routes->first()->operator_id,
                'scheduled_at' => now()->addDays(1)->setHour(8)->setMinute(0),
                'status' => TripStatus::SCHEDULED,
                'price' => $routes->first()->base_price,
                'available_seats' => 20,
                'booked_seats' => 0,
            ],
            [
                'route_id' => $routes->first()->id,
                'vehicle_id' => $vehicles->skip(1)->first()?->id ?? $vehicles->first()->id,
                'driver_profile_id' => $driverProfiles->skip(1)->first()?->id ?? $driverProfiles->first()->id,
                'operator_id' => $routes->first()->operator_id,
                'scheduled_at' => now()->addDays(1)->setHour(14)->setMinute(0),
                'status' => TripStatus::SCHEDULED,
                'price' => $routes->first()->base_price,
                'available_seats' => 20,
                'booked_seats' => 0,
            ],
            [
                'route_id' => $routes->skip(1)->first()?->id ?? $routes->first()->id,
                'vehicle_id' => $vehicles->skip(2)->first()?->id ?? $vehicles->first()->id,
                'driver_profile_id' => $driverProfiles->skip(2)->first()?->id ?? $driverProfiles->first()->id,
                'operator_id' => $routes->skip(1)->first()?->operator_id ?? $routes->first()->operator_id,
                'scheduled_at' => now()->addDays(2)->setHour(9)->setMinute(0),
                'status' => TripStatus::SCHEDULED,
                'price' => $routes->skip(1)->first()?->base_price ?? $routes->first()->base_price,
                'available_seats' => 25,
                'booked_seats' => 0,
            ],
        ];

        $createdTrips = [];
        foreach ($trips as $tripData) {
            $trip = Trip::firstOrCreate(
                [
                    'route_id' => $tripData['route_id'],
                    'scheduled_at' => $tripData['scheduled_at'],
                    'vehicle_id' => $tripData['vehicle_id'],
                ],
                $tripData
            );
            $createdTrips[] = $trip;
        }

        // Create some sample bookings
        $passengerProfiles = Profile::where('profile_type', ProfileType::PASSENGER)->where('active', true)->get();

        if ($passengerProfiles->isNotEmpty() && count($createdTrips) > 0) {
            $bookings = [
                [
                    'trip_id' => $createdTrips[0]->id,
                    'passenger_profile_id' => $passengerProfiles->first()->id,
                    'seat_count' => 2,
                    'status' => BookingStatus::CONFIRMED,
                    'total_price' => $createdTrips[0]->price * 2,
                ],
                [
                    'trip_id' => $createdTrips[0]->id,
                    'passenger_profile_id' => $passengerProfiles->skip(1)->first()?->id ?? $passengerProfiles->first()->id,
                    'seat_count' => 1,
                    'status' => BookingStatus::CONFIRMED,
                    'total_price' => $createdTrips[0]->price,
                ],
            ];

            foreach ($bookings as $bookingData) {
                Booking::firstOrCreate(
                    [
                        'trip_id' => $bookingData['trip_id'],
                        'passenger_profile_id' => $bookingData['passenger_profile_id'],
                    ],
                    $bookingData
                );
            }
        }

        $this->command->info('Trips and Bookings seeded successfully!');
    }
}