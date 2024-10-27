<?php

namespace NiftyCo\OgImage;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support;
use Illuminate\Support\Facades\RateLimiter;
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

        RateLimiter::for('og-image', fn(Request $request) => Limit::perSecond(1)->by($request->ip()));

        $this->app->make('router')->get('/og-image', OGImageController::class)
            ->name('og-image')
            ->middleware('throttle:og-image');
    }
}
