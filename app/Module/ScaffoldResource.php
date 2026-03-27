<?php

namespace App\Console\Commands\Module;

use App\Utils\ModuleScaffold;
use Illuminate\Console\Command;

class ScaffoldResource extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:resource
                            {module : The name of the module}
                            {model : the model to create the resource for}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new model api resource';

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

        $this->info('Creating resource: ' . $model);
        $scaffold = new ModuleScaffold($module, $model);
        if (!$scaffold->createResource()) {
            $this->error('Failed to create resource');
            return 1;
        }

        $this->info('Resource created successfully');
        return 0;
    }
}
