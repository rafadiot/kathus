<?php

use Rafadiot\Kathus\Exceptions\KathusNotFoundException;

if (!function_exists('kathus')) {
    /**
     * Get modules repository.
     *
     * @param string $location
     * @return \Rafadiot\Kathus\RepositoryManager | \Rafadiot\Kathus\Repositories\Repository
     */
    function kathus($location = null)
    {
        if ($location) {
            return app('kathus')->location($location);
        }

        return app('kathus');
    }
}

if (!function_exists('module_path')) {
    /**
     * Return the path to the given module file.
     *
     * @param string $slug
     * @param string $file
     *
     * @param null $location
     * @return string
     * @throws \Rafadiot\Kathus\Exceptions\KathusNotFoundException
     */
    function module_path($slug = null, $file = '', $location = null)
    {
        $location = $location ?: config('kathus.default_location');
        $modulesPath = config("kathus.locations.$location.path");
        $mapping = config("kathus.locations.$location.mapping");

        $filePath = $file ? '/' . ltrim($file, '/') : '';

        if (is_null($slug)) {
            if (empty($file)) {
                return $modulesPath;
            }

            return $modulesPath . $filePath;
        }

        $module = Kathus::location($location)->where('slug', $slug);

        if (is_null($module)) {
            throw new KathusNotFoundException($slug);
        }

        return $modulesPath . '/' . $module['basename'] . $filePath;
    }
}

if (!function_exists('module_class')) {
    /**
     * Return the full path to the given module class.
     *
     * @param string $slug
     * @param string $class
     * @param string $location
     * @return string
     * @throws \Rafadiot\Kathus\Exceptions\KathusNotFoundException
     */
    function module_class($slug, $class, $location = null)
    {
        $location = $location ?: config('kathus.default_location');
        $module = kathus($location)->where('slug', $slug);

        if (is_null($module)) {
            throw new KathusNotFoundException($slug);
        }

        $namespace = config("kathus.locations.$location.namespace") . $module['basename'];

        return "$namespace\\$class";
    }
}
