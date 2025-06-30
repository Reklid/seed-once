<?php

namespace Reklid\SeedOnce\Providers;

use Illuminate\Container\Container;
use Illuminate\Support\ServiceProvider;
use Reklid\SeedOnce\Console\Commands\DatabaseSeed;
use Reklid\SeedOnce\Services\SeederManager;
use Reklid\SeedOnce\Services\SeederRegistry;
use Reklid\SeedOnce\Services\SeederRunner;
use Reklid\SeedOnce\Services\SeederScanner;

class SeedOnceServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(SeederRegistry::class, function () {
            return new SeederRegistry();
        });

        $this->app->singleton(SeederScanner::class, function ($app) {
            return new SeederScanner($app->make(SeederRegistry::class));
        });

        $this->app->singleton(SeederRunner::class, function (Container $app) {
            return new SeederRunner($app);
        });

        $this->app->singleton(SeederManager::class, function ($app) {
            return new SeederManager(
                $app->make(SeederScanner::class),
                $app->make(SeederRegistry::class),
                $app->make(SeederRunner::class),
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        if ($this->app->runningInConsole()) {
            $this->commands([
                DatabaseSeed::class,
            ]);
        }
    }
}
