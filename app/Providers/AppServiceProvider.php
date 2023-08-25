<?php

namespace App\Providers;

use App\Services\OtpService;
use App\Services\UserService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->bind('otp', function () {
            return new OtpService();
        });
        $this->app->bind(UserService::class, function () {
            return new UserService();
        });
    }
}
