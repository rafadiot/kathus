<?php

namespace Rafadiot\Kathus\Console\Generators;

use Rafadiot\Kathus\Console\GeneratorCommand;

class MakeTestCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kathus:module:make:test
    	{slug : The slug of the module}
    	{name : The name of the test class}
    	{--location= : The modules location to create the test class in}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new module test class';

    /**
     * String to store the command type.
     *
     * @var string
     */
    protected $type = 'Module test';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/test.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param $rootNamespace
     * @return string
     * @throws \Rafadiot\Kathus\Exceptions\KathusNotFoundException
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return module_class($this->argument('slug'), 'Tests', $this->option('location'));
    }
}
