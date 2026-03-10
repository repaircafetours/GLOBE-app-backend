<?php

namespace App\Providers;

use App\Http\Services\ItemService;
use App\Http\Services\Logs\ItemLoggerService;
use App\Http\Services\Logs\VisitorLoggerService;
use App\Http\Services\VisitorService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(VisitorLoggerService::class, function ($app) {
            return new VisitorLoggerService();
        });

        $this->app->singleton(VisitorService::class, function ($app) {
            return new VisitorService(
                $app->make(VisitorLoggerService::class)
            );
        });
        $this->app->make(VisitorLoggerService::class)->initialize($this->app->make(VisitorService::class));



        /*$this->app->singleton((ItemLoggerService::class), function($app)  {
            return new ItemLoggerService();
        });
        $this->app->singleton(ItemService::class, function ($app) {
            return new ItemService(
                $app->make(ItemLoggerService::class)
            );
        });*/
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
