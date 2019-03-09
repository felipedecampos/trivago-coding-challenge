<?php

namespace App\Providers;

use App\Repositories\WineSpectatorRepository;
use App\Services\WineSpectatorService;
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
                $app->get(WineSpectatorRepository::class)
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
        //
    }
}
