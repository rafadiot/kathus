<?php

namespace Kathus\Console\Generators;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Kathus\RepositoryManager;
use Symfony\Component\Console\Helper\ProgressBar;

class MakeKathusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kathus:make:module
        {slug : The slug of the module}
        {--Q|quick : Skip the make:module wizard and use default values}
        {--location= : The modules location.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Kathus module and bootstrap it';

    /**
     * The modules instance.
     *
     * @var RepositoryManager
     */
    protected $module;

    /**
     * The filesystem instance.
     *
     * @var Filesystem
     */
    protected $files;

    /**
     * Array to store the configuration details.
     *
     * @var array
     */
    protected $container;

    /**
     * Create a new command instance.
     *
     * @param Filesystem $files
     * @param RepositoryManager $module
     */
    public function __construct(Filesystem $files, RepositoryManager $module)
    {
        parent::__construct();

        $this->files = $files;
        $this->module = $module;
    }

    /**
     * Execute the console command.
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle()
    {
        $location = $this->option('location');

        $this->container['slug'] = Str::slug($this->argument('slug'));
        $this->container['name'] = Str::studly($this->container['slug']);
        $this->container['version'] = '1.0';
        $this->container['description'] = 'This is the description for the ' . $this->container['name'] . ' module.';
        $this->container['location'] = $location ?: config('kathus.default_location');
        $this->container['provider'] = config("kathus.locations.{$this->container['location']}.provider");

        if ($this->option('quick')) {
            $this->container['basename'] = Str::studly($this->container['slug']);
            $this->container['namespace'] = config("kathus.locations.{$this->container['location']}.namespace") . $this->container['basename'];

            return $this->generate();
        }

        $this->displayHeader('introduction');
        $this->stepOne();
    }

    /**
     * Generate the module.
     */
    protected function generate()
    {
        $steps = [
            'Generating module...' => 'generateModule',
            'Optimizing module cache...' => 'optimizeModules',
        ];

        $progress = new ProgressBar($this->output, count($steps));
        $progress->start();

        foreach ($steps as $message => $function) {
            $progress->setMessage($message);
            $this->$function();
            $progress->advance();
        }

        $progress->finish();

        event($this->container['slug'] . '.module.made');

        $this->info("\nModule generated successfully.");
    }

    /**
     * Pull the given stub file contents and display them on screen.
     *
     * @param string $file
     * @param string $level
     * @return mixed
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function displayHeader($file = '', $level = 'info')
    {
        $stub = $this->files->get(__DIR__ . '/../../../resources/stubs/console/' . $file . '.stub');

        return $this->$level($stub);
    }

    /**
     * Step 1: Configure module manifest.
     *
     * @return bool
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function stepOne()
    {
        $this->displayHeader('run');

        $this->container['name'] = $this->ask('Please enter the name of the module:', $this->container['name']);
        $this->container['slug'] = $this->ask('Please enter the slug for the module:', $this->container['slug']);
        $this->container['version'] = $this->ask('Please enter the module version:', $this->container['version']);
        $this->container['description'] = $this->ask('Please enter the description of the module:', $this->container['description']);
        $this->container['basename'] = Str::studly($this->container['slug']);
        $this->container['namespace'] = config("kathus.locations.{$this->container['location']}.namespace") . $this->container['basename'];

        $this->comment('You have provided the following manifest information:');
        $this->comment('Name:                       ' . $this->container['name']);
        $this->comment('Slug:                       ' . $this->container['slug']);
        $this->comment('Version:                    ' . $this->container['version']);
        $this->comment('Description:                ' . $this->container['description']);
        $this->comment('Basename (auto-generated):  ' . $this->container['basename']);
        $this->comment('Namespace (auto-generated): ' . $this->container['namespace']);

        if ($this->confirm('If the provided information is correct, type "yes" to generate.')) {
            $this->comment('Thanks! That\'s all we need.');
            $this->comment('Drink coffee while your module is generated.');
            $this->generate();
        } else {
            return $this->stepOne();
        }

        return true;
    }

    /**
     * Generate defined module folders.
     */
    protected function generateModule()
    {
        $location = $this->container['location'];
        $root = kathus_path(null, '', $location);
        $manifest = config("kathus.locations.$location.manifest") ?: 'module.json';
        $provider = config("kathus.locations.$location.provider") ?: 'KathusServiceProvider';

        if (!$this->files->isDirectory($root)) {
            $this->files->makeDirectory($root);
        }

        $mapping = config("kathus.locations.$location.mapping");
        $directory = kathus_path(null, $this->container['basename'], $location);
        $source = __DIR__ . '/../../../resources/stubs/module';

        $this->files->makeDirectory($directory);
        $sourceFiles = $this->files->allFiles($source, true);

        if (!empty($mapping)) {
            $search = array_keys($mapping);
            $replace = array_values($mapping);
        }

        foreach ($sourceFiles as $file) {
            $contents = $this->replacePlaceholders($file->getContents());
            $subPath = $file->getRelativePathname();

            if (!empty($mapping)) {
                $subPath = str_replace($search, $replace, $subPath);
            }

            $filePath = $directory . '/' . $subPath;

            // If the file is module.json, replace it with the custom manifest file name
            if ($file->getFilename() === 'module.json' && $manifest) {
                $filePath = str_replace('module.json', $manifest, $filePath);
            }

            // If the file is ModuleServiceProvider.php, replace it with the custom provider file name
            if ($file->getFilename() === 'KathusServiceProvider.php') {
                $filePath = str_replace('KathusServiceProvider', $provider, $filePath);
            }

            $dir = dirname($filePath);

            if (!$this->files->isDirectory($dir)) {
                $this->files->makeDirectory($dir, 0755, true);
            }

            $this->files->put($filePath, $contents);
        }
    }

    /**
     * @param $contents
     * @return mixed
     */
    protected function replacePlaceholders($contents)
    {
        $location = $this->container['location'];
        $mapping = config("kathus.locations.$location.mapping");

        $find = [
            'DummyBasename',
            'DummyNamespace',
            'DummyName',
            'DummySlug',
            'DummyVersion',
            'DummyDescription',
            'DummyLocation',
            'DummyProvider',
            'ConfigMapping',
            'DatabaseFactoriesMapping',
            'DatabaseMigrationsMapping',
            'DatabaseSeedsMapping',
            'HttpControllersMapping',
            'HttpMiddlewareMapping',
            'ProvidersMapping',
            'ResourcesLangMapping',
            'ResourcesViewsMapping',
            'RoutesMapping',
        ];

        $replace = [
            $this->container['basename'],
            $this->container['namespace'],
            $this->container['name'],
            $this->container['slug'],
            $this->container['version'],
            $this->container['description'],
            $this->container['location'] ?? config('kathus.default_location'),
            $this->container['provider'],

            $mapping['Config'] ?? 'Config',
            $mapping['Database/Factories'] ?? 'Database/Factories',
            $mapping['Database/Migrations'] ?? 'Database/Migrations',
            $mapping['Database/Seeds'] ?? 'Database/Seeds',
            $mapping['Http/Controllers'] ?? 'Http/Controllers',
            $mapping['Http/Middleware'] ?? 'Http/Middleware',
            $mapping['Providers'] ?? 'Providers',
            $mapping['Resources/Lang'] ?? 'Resources/Lang',
            $mapping['Resources/Views'] ?? 'Resources/Views',
            $mapping['Routes'] ?? 'Routes'
        ];

        return str_replace($find, $replace, $contents);
    }

    /**
     * Reset module cache of enabled and disabled modules.
     */
    protected function optimizeModules()
    {
        return $this->callSilent('kathus:module:optimize');
    }
}
