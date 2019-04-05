<?php

namespace DummyNamespace\Providers;

use Kathus\Support\ServiceProvider;

class DummyProvider extends ServiceProvider
{
    /**
     * Bootstrap the module services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadTranslationsFrom(kathus_path('DummySlug', 'ResourcesLangMapping', 'DummyLocation'), 'DummySlug');
        $this->loadViewsFrom(kathus_path('DummySlug', 'ResourcesViewsMapping', 'DummyLocation'), 'DummySlug');
        $this->loadMigrationsFrom(kathus_path('DummySlug', 'DatabaseMigrationsMapping', 'DummyLocation'), 'DummySlug');
        $this->loadConfigsFrom(kathus_path('DummySlug', 'ConfigMapping', 'DummyLocation'));
        $this->loadFactoriesFrom(kathus_path('DummySlug', 'DatabaseFactoriesMapping', 'DummyLocation'));
    }

    /**
     * Register the module services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
    }
}
