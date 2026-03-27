<?php

namespace App\Console\Commands\Module;


use App\Utils\ModuleScaffold;
use Illuminate\Console\Command;

class ScaffoldRepository extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:repository
                            {module : The name of the module}
                            {model : the first model to add}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new repository';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        return $this->init();
    }

    private function init()
    {
        $module = $this->argument('module');
        $model = $this->argument('model');

        $this->alert('Setting up module: ' . $module);

        $this->info('Creating repository: ' . $model);
        $scaffold = new ModuleScaffold($module, $model);
        if (!$scaffold->createRepository()) {
            $this->error('Failed to create repository');
            return 1;
        }

        $this->info('Repository created successfully');
        return 0;
    }
}
