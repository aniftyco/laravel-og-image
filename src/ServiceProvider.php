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
        $this->app->singleton('og-image.generator', function ($app) {
            return new Generator($app['view']);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->make('router')->get('/og-image', function (Request $request) {
            return $this->app->make('og-image.generator')->make($request->get('template'), $request->except('template'));
        });
    }
}
