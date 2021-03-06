<?php

namespace Rafadiot\Kathus;

use Illuminate\Support\ServiceProvider;
use Rafadiot\Kathus\Providers\BladeServiceProvider;
use Rafadiot\Kathus\Providers\ConsoleServiceProvider;
use Rafadiot\Kathus\Providers\GeneratorServiceProvider;

class KathusServiceProvider extends ServiceProvider
{
    /**
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the provided services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/kathus.php' => config_path('kathus.php'),
        ], 'config');

        $this->app['kathus']->register();
    }

    /**
     * Register the provided services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/kathus.php', 'kathus'
        );

        $this->app->register(ConsoleServiceProvider::class);
        $this->app->register(GeneratorServiceProvider::class);
        $this->app->register(BladeServiceProvider::class);

        $this->app->singleton('kathus', function ($app) {
            return new RepositoryManager($app);
        });
    }

    /**
     * Get the services provided by the package.
     *
     * @return array
     */
    public function provides()
    {
        return ['kathus'];
    }

    /**
     * Register compilable code.
     *
     * @return array
     * @throws Exceptions\KathusNotFoundException
     */
    public static function compiles()
    {
        $files = [];

        foreach (kathus()->repositories() as $repository) {
            foreach ($repository->all() as $module) {
                $serviceProvider = module_class($module['slug'], 'Providers\\KathusServiceProvider', $repository->location);

                if (class_exists($serviceProvider)) {
                    $files = array_merge($files, forward_static_call([$serviceProvider, 'compiles']));
                }
            }
        }

        return array_map('realpath', $files);
    }
}
