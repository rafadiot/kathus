<?php

namespace Rafadiot\Kathus\Exceptions;

class KathusNotFoundException extends \Exception
{
    /**
     * KathusNotFoundException constructor.
     *
     * @param $slug
     */
    public function __construct($slug)
    {
        parent::__construct('Module with slug name [' . $slug . '] not found');
    }
}
