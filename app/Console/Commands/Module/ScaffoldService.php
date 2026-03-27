<?php

namespace App\Console\Commands\Module;


use App\Utils\ModuleScaffold;
use Illuminate\Console\Command;

class ScaffoldService extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:service
                            {module : The name of the module}
                            {model : the model to create the service for}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new model api service';

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

        $this->info('Creating service: ' . $model);
        $scaffold = new ModuleScaffold($module, $model);
        if (!$scaffold->createService()) {
            $this->error('Failed to create service');
            return 1;
        }

        $this->info('Service created successfully');
        return 0;
    }
}
