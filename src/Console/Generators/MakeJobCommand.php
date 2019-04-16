<?php

namespace Kathus\Console\Generators;

use Kathus\Console\GeneratorCommand;

class MakeJobCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kathus:module:make:job
    	{slug : The slug of the module.}
    	{name : The name of the job class.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new module job class';

    /**
     * String to store the command type.
     *
     * @var string
     */
    protected $type = 'Module job';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/stubs/job.stub';
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
        return kathus_class($this->argument('slug'), 'Jobs');
    }
}