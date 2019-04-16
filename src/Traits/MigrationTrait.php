<?php

namespace Kathus\Traits;

trait MigrationTrait
{
    /**
     * Require (once) all migration files for the supplied module.
     *
     * @param $module
     */
    protected function requireMigrations($module)
    {
        $path = $this->getMigrationPath($module);

        $migrations = $this->laravel['files']->glob($path . '*_*.php');

        foreach ($migrations as $migration) {
            $this->laravel['files']->requireOnce($migration);
        }
    }

    /**
     * Get migration directory path.
     *
     * @param $module
     * @return \Illuminate\Config\Repository|mixed|string
     */
    protected function getMigrationPath($module)
    {
        return kathus_path($module, 'Database/Migrations');
    }
}
