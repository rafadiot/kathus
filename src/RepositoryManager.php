<?php

namespace Kathus;

use Exception;
use Illuminate\Foundation\Application;
use Kathus\Repositories\Repository;
use Kathus\Exceptions\KathusNotFoundException;

class RepositoryManager
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var array
     */
    protected $repositories = [];

    /**
     * Create a new Modules instance.
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @param null $location
     * @return mixed
     * @throws Exception
     */
    public function location($location = null)
    {
        return $this->repository($location);
    }

    /**
     * Register the module service provider file from all modules.
     *
     * @throws Exception
     */
    public function register()
    {
        foreach (array_keys(config('kathus.locations')) as $location) {
            $repository = $this->repository($location);
            $modules = $repository->enabled();

            $modules->each(function ($module) use ($repository) {
                try {
                    $this->registerServiceProvider($repository, $module);
                    $this->autoloadFiles($module);
                } catch (KathusNotFoundException $e) {
                    //
                }
            });
        }
    }

    /**
     * Register the module service provider.
     *
     * @param Repository $repository
     * @param $module
     * @throws KathusNotFoundException
     */
    private function registerServiceProvider(Repository $repository, $module)
    {
        $location = $repository->location;
        $provider = config("kathus.location.$location.provider", 'Providers\\KathusServiceProvider');
        $serviceProvider = kathus_class($module['slug'], $provider, $location);

        if (class_exists($serviceProvider)) {
            $this->app->register($serviceProvider);
        }
    }

    /**
     * Autoload custom module files.
     *
     * @param $module
     * @throws KathusNotFoundException
     */
    private function autoloadFiles($module)
    {
        if (isset($module['autoload'])) {
            foreach ($module['autoload'] as $file) {
                $path = kathus_path($module['slug'], $file);

                if (file_exists($path)) {
                    include $path;
                }
            }
        }
    }

    /**
     * @return array
     */
    public function repositories()
    {
        return $this->repositories;
    }

    /**
     * @param null $location
     * @return mixed
     * @throws Exception
     */
    protected function repository($location = null)
    {
        $location = $location ?: config('kathus.default_location');
        $driverClass = $this->repositoryClass($location);

        if (!$driverClass) {
            throw new Exception("[$location] not found. Check your module locations configuration.");
        }

        return $this->repositories[$location]
            ?? $this->repositories[$location] = new $driverClass($location, $this->app['config'], $this->app['files']);
    }

    /**
     * @param $location
     * @return \Illuminate\Config\Repository|mixed
     * @throws Exception
     */
    protected function repositoryClass($location)
    {
        $locationConfig = config("kathus.locations.$location");

        if (is_null($locationConfig)) {
            throw new Exception("Location [$location] not configured. Please check your kathus.php configuration file.");
        }

        $driver = $locationConfig['driver'] ?? config('kathus.default_driver');

        return config("kathus.drivers.$driver");
    }

    /**
     * @param $method
     * @param $arguments
     * @return mixed
     * @throws Exception
     */
    public function __call($method, $arguments)
    {
        return call_user_func_array([$this->repository(), $method], $arguments);
    }
}
