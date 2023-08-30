<?php

namespace App\Providers;

use App\Services\OtpService;
use App\Services\Sms\SmsProviderService;
use App\Services\Sms\SmsService;
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
        $this->app->bind(UserService::class, function ($app) {
            return new UserService($app->make(SmsService::class));
        });
        $this->app->bind(SmsService::class, function ($app) {
            return new SmsService($app->make(SmsProviderService::class));
        });
    }
}
