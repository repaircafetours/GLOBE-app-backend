<?php
namespace App\Providers;

use App\Http\Services\Logs\LogsService;
use App\Http\Services\Logs\VisitorLoggerService;
use App\Http\Services\VisitorService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(LogsService::class, function ($app) {
            return new LogsService();
        });

        $this->app->singleton(VisitorLoggerService::class, function ($app) {
            //throw $app->make(LogsService::class);
            return new VisitorLoggerService($app->make(LogsService::class));
        });

        $this->app->singleton(VisitorService::class, function ($app) {
            $visitorLogger = $app->make(VisitorLoggerService::class);
            $service = new VisitorService($visitorLogger);
            $visitorLogger->initialize($service);
            return $service;
        });
    }

    public function boot(): void {}
}
