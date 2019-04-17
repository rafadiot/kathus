<?php

namespace Rafadiot\Kathus\Support;

use Illuminate\Support\Facades\Gate as IlluminateGate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [];

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Register the application's policies.
     *
     * @return void
     */
    public function registerPolicies()
    {
        foreach ($this->policies as $key => $value) {
            IlluminateGate::policy($key, $value);
        }
    }
}
