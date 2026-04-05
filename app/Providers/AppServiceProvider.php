<?php
namespace App\Providers;

use App\Http\Services\ItemService;
use App\Http\Services\Logs\ItemLoggerService;
use App\Http\Services\Logs\LogsService;
use App\Http\Services\Logs\VisitorLoggerService;
use App\Http\Services\Logs\VolunteerLoggerService;
use App\Http\Services\VisitorService;
use App\Http\Services\VolunteerService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(LogsService::class, function ($app) {
            return new LogsService();
        });

        // ----------------------------------------------------------------
        // Visitor
        // ----------------------------------------------------------------
        $this->app->singleton(VisitorLoggerService::class, function ($app) {
            return new VisitorLoggerService($app->make(LogsService::class));
        });

        $this->app->singleton(VisitorService::class, function ($app) {
            $logger = $app->make(VisitorLoggerService::class);
            $service = new VisitorService($logger);
            $logger->initialize($service);
            return $service;
        });

        // ----------------------------------------------------------------
        // Volunteer
        // ----------------------------------------------------------------
        $this->app->singleton(VolunteerLoggerService::class, function ($app) {
            return new VolunteerLoggerService($app->make(LogsService::class));
        });

        $this->app->singleton(VolunteerService::class, function ($app) {
            $logger = $app->make(VolunteerLoggerService::class);
            $service = new VolunteerService($logger);
            $logger->initialize($service);
            return $service;
        });

        // ----------------------------------------------------------------
        // Item
        // ----------------------------------------------------------------
        $this->app->singleton(ItemLoggerService::class, function ($app) {
            return new ItemLoggerService($app->make(LogsService::class));
        });

        $this->app->singleton(ItemService::class, function ($app) {
            $logger = $app->make(ItemLoggerService::class);
            $service = new ItemService($logger);
            $logger->initialize($service);
            return $service;
        });
    }

    public function boot(): void {}
}
