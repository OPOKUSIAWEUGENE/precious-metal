<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Modules\v0\Users\Interfaces\ProfileInterface;
use App\Modules\v0\Users\Interfaces\RoleInterface;
use App\Modules\v0\Users\Interfaces\UserInterface;
use App\Modules\v0\Users\Repositories\ProfileRepository;
use App\Modules\v0\Users\Repositories\RoleRepository;
use App\Modules\v0\Users\Repositories\UserRepository;
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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
    }
}
