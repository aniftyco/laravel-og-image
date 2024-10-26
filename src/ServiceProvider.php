<?php

namespace NiftyCo\OgImage;

use Illuminate\Support;
use NiftyCo\OgImage\Controllers\OGImageController;

class ServiceProvider extends Support\ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/og-image.php',
            'og-image'
        );

        $this->app->singleton('og-image.generator', function ($app) {
            return new Generator($app['view'], $app['config']['og-image']);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Commands\ClearOGImageCache::class,
            ]);
        }

        $this->publishes([
            __DIR__ . '/../config/og-image.php' => config_path('og-image.php'),
        ]);

        $this->app->make('router')->get('/og-image', OGImageController::class);
    }
}
