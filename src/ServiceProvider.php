<?php

namespace NiftyCo\OgImage;

use Illuminate\Http\Request;
use Illuminate\Support;

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

        $this->app->make('router')->get('/og-image', function (Request $request) {
            $hash = md5(json_encode($request->query()));
            $dir = storage_path('framework/cache/og-images');
            $path = "{$dir}/{$hash}.png";

            // Ensure the directory exists
            if (!$this->app->make('files')->exists($dir)) {
                $this->app->make('files')->makeDirectory($dir);
            }

            // does the image already exist?
            if ($this->app->make('files')->exists($path)) {
                return response()->file($path, headers: ['Content-Type' => 'image/png']);
            }

            $image = $this->app->make('og-image.generator')->make($request->get('template'), $request->except('template'));

            $this->app->make('files')->put($path, $image->toPng());

            return $image;
        });
    }
}
