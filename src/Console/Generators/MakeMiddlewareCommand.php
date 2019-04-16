<?php

namespace Kathus\Console\Generators;

use Kathus\Console\GeneratorCommand;

class MakeMiddlewareCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kathus:module:make:middleware
    	{slug : The slug of the module.}
    	{name : The name of the middleware class.}
    	{--location= : The modules location to create the module middleware class in}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new module middleware class';

    /**
     * String to store the command type.
     *
     * @var string
     */
    protected $type = 'Module middleware';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/stubs/middleware.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param string $rootNamespace
     * @return string
     * @throws \Kathus\Exceptions\KathusNotFoundException
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return kathus_class($this->argument('slug'), 'Http\\Middleware', $this->option('location'));
    }
}
