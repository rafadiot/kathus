<?php

namespace Rafadiot\Kathus\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the provided services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the provided services.
     *
     * @return void
     */
    public function register()
    {
        $default = config('kathus.default_driver');
        $driver = config('kathus.drivers.' . $default);
        $this->app->bind('Rafadiot\Kathus\Contracts\Repository', $driver);
    }
}
