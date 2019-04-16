<?php

namespace Kathus\Providers;

use Illuminate\Support\ServiceProvider;

class GeneratorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the provided services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the provided services.
     *
     * @return void
     */
    public function register()
    {
        $generators = [
            'command.make.module' => \Kathus\Console\Generators\MakeModuleCommand::class,
            'command.make.module.controller' => \Kathus\Console\Generators\MakeControllerCommand::class,
            'command.make.module.middleware' => \Kathus\Console\Generators\MakeMiddlewareCommand::class,
            'command.make.module.migration' => \Kathus\Console\Generators\MakeMigrationCommand::class,
            'command.make.module.model' => \Kathus\Console\Generators\MakeModelCommand::class,
            'command.make.module.policy' => \Kathus\Console\Generators\MakePolicyCommand::class,
            'command.make.module.provider' => \Kathus\Console\Generators\MakeProviderCommand::class,
            'command.make.module.request' => \Kathus\Console\Generators\MakeRequestCommand::class,
            'command.make.module.seeder' => \Kathus\Console\Generators\MakeSeederCommand::class,
            'command.make.module.test' => \Kathus\Console\Generators\MakeTestCommand::class,
            'command.make.module.job' => \Kathus\Console\Generators\MakeJobCommand::class,
        ];

        foreach ($generators as $slug => $class) {
            $this->app->singleton($slug, function ($app) use ($slug, $class) {
                return $app[$class];
            });

            $this->commands($slug);
        }
    }
}
