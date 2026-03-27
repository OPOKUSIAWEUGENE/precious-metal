<?php

namespace App\Console\Commands\Module;


use App\Utils\ModuleScaffold;
use Illuminate\Console\Command;

class ScaffoldInterface extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:interface {module : The name of the module}
                            {model : the  model  interface to create}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create an interface in the module for the model';

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

        $this->alert('Setting up interface in module: ' . $module);

        $this->info('Creating interface: ' . $model . 'Interface');
        $scaffold = new ModuleScaffold($module, $model);
        if (!$scaffold->createInterface()) {
            $this->error('Failed to create interface');
            return 1;
        }

        $this->info('Interface created successfully');
        return 0;
    }
}
