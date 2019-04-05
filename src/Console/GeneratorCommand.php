<?php

namespace Kathus\Console;

use Kathus;
use Illuminate\Console\GeneratorCommand as LaravelGeneratorCommand;
use Illuminate\Support\Str;

abstract class GeneratorCommand extends LaravelGeneratorCommand
{
    /**
     * Parse the name and format according to the root namespace.
     *
     * @param string $name
     * @return string
     */
    protected function qualifyClass($name)
    {
        try {
            $location = $this->option('location') ?: config('kathus.default_location');
        }
        catch (\Exception $e) {
            $location = config('kathus.default_location');
        }

        $rootNamespace = config("kathus.locations.$location.namespace");

        if (Str::startsWith($name, $rootNamespace)) {
            return $name;
        }

        $name = str_replace('/', '\\', $name);

        return $this->qualifyClass(
            $this->getDefaultNamespace(trim($rootNamespace, '\\')).'\\'.$name
        );
    }

    /**
     * Get the destination class path.
     *
     * @param string $name
     * @return string
     */
    protected function getPath($name)
    {
        try {
            $location = $this->option('location') ?: config('kathus.default_location');
        }
        catch (\Exception $e) {
            $location = config('kathus.default_location');
        }

        $slug = $this->argument('slug');
        $module = Kathus::location($location)->where('slug', $slug);

        // Take everything after the module name in the given path (ignoring case)
        $key = array_search(strtolower($module['basename']), explode('\\', strtolower($name)));

        if ($key === false) {
            $newPath = str_replace('\\', '/', $name);
        } else {
            $newPath = implode('/', array_slice(explode('\\', $name), $key + 1));
        }

        return kathus_path($slug, "$newPath.php", $location);
    }
}
