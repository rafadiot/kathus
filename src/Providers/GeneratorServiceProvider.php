<?php

namespace Rafadiot\Kathus\Providers;

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
            'command.make.module' => \Rafadiot\Kathus\Console\Generators\MakeKathusCommand::class,
            'command.make.module.controller' => \Rafadiot\Kathus\Console\Generators\MakeControllerCommand::class,
            'command.make.module.middleware' => \Rafadiot\Kathus\Console\Generators\MakeMiddlewareCommand::class,
            'command.make.module.migration' => \Rafadiot\Kathus\Console\Generators\MakeMigrationCommand::class,
            'command.make.module.model' => \Rafadiot\Kathus\Console\Generators\MakeModelCommand::class,
            'command.make.module.policy' => \Rafadiot\Kathus\Console\Generators\MakePolicyCommand::class,
            'command.make.module.provider' => \Rafadiot\Kathus\Console\Generators\MakeProviderCommand::class,
            'command.make.module.request' => \Rafadiot\Kathus\Console\Generators\MakeRequestCommand::class,
            'command.make.module.seeder' => \Rafadiot\Kathus\Console\Generators\MakeSeederCommand::class,
            'command.make.module.test' => \Rafadiot\Kathus\Console\Generators\MakeTestCommand::class,
            'command.make.module.job' => \Rafadiot\Kathus\Console\Generators\MakeJobCommand::class,
        ];

        foreach ($generators as $slug => $class) {
            $this->app->singleton($slug, function ($app) use ($slug, $class) {
                return $app[$class];
            });

            $this->commands($slug);
        }
    }
}
