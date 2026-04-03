<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Modules\v0\User\Interfaces\UserInterface;
use App\Modules\v0\User\Repositories\UserRepository;
use App\Modules\v0\User\Interfaces\ProfileInterface;
use App\Modules\v0\User\Repositories\ProfileRepository;
use App\Modules\v0\User\Interfaces\RoleInterface;
use App\Modules\v0\User\Repositories\RoleRepository;

use App\Modules\v0\Finances\Interfaces\CommissionInterface;
use App\Modules\v0\Finances\Repositories\CommissionRepository;
use App\Modules\v0\Finances\Interfaces\PaymentInterface;
use App\Modules\v0\Finances\Repositories\PaymentRepository;
use App\Modules\v0\Finances\Interfaces\SettlementInterface;
use App\Modules\v0\Finances\Repositories\SettlementRepository;

use App\Modules\v0\Trips\Interfaces\TripInterface;
use App\Modules\v0\Trips\Repositories\TripRepository;
use App\Modules\v0\Trips\Interfaces\BookingInterface;
use App\Modules\v0\Trips\Repositories\BookingRepository;

use App\Modules\v0\Routes\Interfaces\RouteInterface;
use App\Modules\v0\Routes\Repositories\RouteRepository;
use App\Modules\v0\Routes\Interfaces\RouteStopInterface;
use App\Modules\v0\Routes\Repositories\RouteStopRepository;

use App\Modules\v0\Fleets\Interfaces\VehicleInterface;
use App\Modules\v0\Fleets\Repositories\VehicleRepository;
use App\Modules\v0\Fleets\Interfaces\DriverVehicleInterface;
use App\Modules\v0\Fleets\Repositories\DriverVehicleRepository;

use App\Modules\v0\Operators\Interfaces\OperatorInterface;
use App\Modules\v0\Operators\Repositories\OperatorRepository;
use Laravel\Sanctum\Sanctum;
use App\Models\PersonalAccessToken;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserInterface::class, UserRepository::class);
        $this->app->bind(ProfileInterface::class, ProfileRepository::class);
        $this->app->bind(RoleInterface::class, RoleRepository::class);

        $this->app->bind(CommissionInterface::class, CommissionRepository::class);
        $this->app->bind(PaymentInterface::class, PaymentRepository::class);
        $this->app->bind(SettlementInterface::class, SettlementRepository::class);

        $this->app->bind(TripInterface::class, TripRepository::class);
        $this->app->bind(BookingInterface::class, BookingRepository::class);

        $this->app->bind(RouteInterface::class, RouteRepository::class);
        $this->app->bind(RouteStopInterface::class, RouteStopRepository::class);

        $this->app->bind(VehicleInterface::class, VehicleRepository::class);
        $this->app->bind(DriverVehicleInterface::class, DriverVehicleRepository::class);

        $this->app->bind(OperatorInterface::class, OperatorRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
    }
}
