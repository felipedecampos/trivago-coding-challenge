<?php

namespace App\Providers;

use App\Repositories\OrderRepository;
use App\Repositories\SommelierRepository;
use App\Repositories\WaiterRepository;
use App\Repositories\WineSpectatorRepository;
use App\Services\OrderService;
use App\Services\WineSpectatorService;
use Illuminate\Container\EntryNotFoundException;
use Illuminate\Foundation\Application;
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
                $app->get(WineSpectatorRepository::class),
                $app->get('log')
            );
        });

        $this->app->singleton(OrderService::class, function (Application $app): OrderService {
            return new OrderService(
                $app->get('db'),
                $app->get(OrderRepository::class),
                $app->get(WaiterRepository::class),
                $app->get(SommelierRepository::class),
                $app->get('log')
            );
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @param Application $app
     * @return void
     */
    public function boot(Application $app)
    {
        try {
            $app->get('db')->listen(function ($query) use ($app) {
                $app->get('log')->channel('queries')->info(
                    $query->sql,
                    $query->bindings,
                    $query->time
                );
            });
        } catch (EntryNotFoundException $e) {
            // exception
        } catch (\Exception $e) {
            // exception
        }
    }
}
