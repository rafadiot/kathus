<?php

namespace Rafadiot\Kathus\Facades;

use Illuminate\Support\Facades\Facade;

class Kathus extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'kathus';
    }
}
