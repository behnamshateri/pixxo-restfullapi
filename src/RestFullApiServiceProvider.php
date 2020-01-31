<?php

namespace Pixxo\RestFullApi;

use Illuminate\Support\ServiceProvider;

class RestFullApiServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function register()
    {
        // register our controller
        $this->app->make('Pixxo\RestFullApi\RestFullApiController');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
    }
}
