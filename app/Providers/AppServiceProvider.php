<?php

namespace App\Providers;

use App\Repositories\OrderRepository;
use App\Repositories\SommelierRepository;
use App\Repositories\WaiterRepository;
use App\Repositories\WineSpectatorRepository;
use App\Services\OrderService;
use App\Services\WaiterService;
use App\Services\WineSpectatorService;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(WineSpectatorService::class, function (Application $app): WineSpectatorService {
            return new WineSpectatorService(
                config('external.wine-spectator.rss'),
                $app->get('db'),
                $app->get(WineSpectatorRepository::class)
            );
        });

        $this->app->singleton(WaiterService::class, function (Application $app): WaiterService {
            return new WaiterService(
                $app->get('db'),
                $app->get(WaiterRepository::class)
            );
        });

        $this->app->singleton(OrderService::class, function (Application $app): OrderService {
            return new OrderService(
                $app->get('db'),
                $app->get(OrderRepository::class),
                $app->get(WaiterRepository::class),
                $app->get(SommelierRepository::class)
            );
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        DB::listen(function($query) {
            Log::info(
                $query->sql,
                $query->bindings,
                $query->time
            );
        });
    }
}
