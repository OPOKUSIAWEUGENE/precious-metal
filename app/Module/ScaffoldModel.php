<?php

namespace App\Console\Commands\Module;


use App\Utils\ModuleScaffold;
use Illuminate\Console\Command;

class ScaffoldModel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:model
                            {module : The name of the module}
                            {model : the first model to add}
                            {--m|migration : create a migration file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new module model with or without the migration';

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

        $migration = (bool)$this->option('migration');

        $this->alert('Setting up module: ' . $module);

        $this->info('Creating model: ' . $model);
        $scaffold = new ModuleScaffold($module, $model, $migration);
        if (!$scaffold->createModel()) {
            $this->error('Failed to create model');
            return 1;
        }

        $this->info('Model created successfully');
        return 0;
    }
}
