<?php

namespace Kathus\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Kathus\RepositoryManager;
use Illuminate\Database\Migrations\Migrator;
use Kathus\Traits\MigrationTrait;
use Kathus\Repositories\Repository;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class KathusMigrateRollbackCommand extends Command
{
    use MigrationTrait, ConfirmableTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'kathus:module:migrate:rollback';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rollback the last database migrations for a specific or all modules';

    /**
     * The migrator instance.
     *
     * @var Migrator
     */
    protected $migrator;

    /**
     * @var RepositoryManager
     */
    protected $module;

    /**
     * Create a new command instance.
     *
     * @param Migrator $migrator
     * @param RepositoryManager $module
     */
    public function __construct(Migrator $migrator, RepositoryManager $module)
    {
        parent::__construct();

        $this->migrator = $migrator;
        $this->module = $module;
    }

    /**
     * Execute the console command.
     *
     * @throws \Exception
     */
    public function handle()
    {
        if (!$this->confirmToProceed()) {
            return;
        }

        $this->migrator->setConnection($this->option('database'));

        $repository = kathus()->location($this->option('location'));
        $paths = $this->getMigrationPaths($repository);

        $this->migrator->setOutput($this->output)->rollback(
            $paths, ['pretend' => $this->option('pretend'), 'step' => (int)$this->option('step')]
        );
    }

    /**
     * Get all of the migration paths.
     *
     * @param Repository $repository
     * @return array
     */
    protected function getMigrationPaths(Repository $repository)
    {
        $slug = $this->argument('slug');
        $paths = [];

        if ($slug) {
            $paths[] = kathus_path($slug, 'Database/Migrations', $repository->location);
        } else {
            foreach ($repository->all() as $module) {
                $paths[] = kathus_path($module['slug'], 'Database/Migrations', $repository->location);
            }
        }

        return $paths;
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [['slug', InputArgument::OPTIONAL, 'Module slug.']];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['database', null, InputOption::VALUE_OPTIONAL, 'The database connection to use.'],
            ['force', null, InputOption::VALUE_NONE, 'Force the operation to run while in production.'],
            ['pretend', null, InputOption::VALUE_NONE, 'Dump the SQL queries that would be run.'],
            ['step', null, InputOption::VALUE_OPTIONAL, 'The number of migrations to be reverted.'],
            ['location', null, InputOption::VALUE_OPTIONAL, 'Which modules location to use.'],
        ];
    }
}
