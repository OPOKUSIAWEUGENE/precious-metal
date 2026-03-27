<?php

namespace App\Console\Commands;

use App\Utils\ModuleScaffold;
use Illuminate\Console\Command;


class ScaffoldModule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:scaffold {module : The name of the module}
                            {model : the first model to add}
                            {--a|all : scaffold and create all instances of model,migration,interface,repository, resource, service and test}
                            {--m|migration : create a migration file}
                            {--i|interface : create an interface file}
                            {--r|repository : create a repository file}
                            {--s|service : create a service file}
                            {--t|test : create a test file}
                            {--R|resource : create a resource file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scaffold a new module';

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
        $this->init();
        return 1;
    }

    private function init()
    {
        $module = $this->argument('module');
        $model = $this->argument('model');

        $migration = (bool)$this->option('migration');
        $all = (bool)$this->option('all');

        $this->alert('Setting up module: ' . $module);
        $scaffold = new ModuleScaffold($module, $model, $all ?? $migration);
        if ($all) {
            $this->info('Scaffolding all module elements');

            if ($scaffold->generate()) {
                $this->info("Module scaffolded successfully");
            } else {
                $this->error("Module scaffold failed \nCheck the logs for more information");
            }

            return;
        }


        $scaffold->createModule();
        if ($scaffold->createModel()) {
            
            if (!$scaffold->createInterface((bool)$this->option('interface'))) {
                $this->error("Interface creation failed \nCheck the logs for more information");
            }

            if (!$scaffold->createRepository((bool)$this->option('repository'))) {
                $this->error("Repository creation failed \nCheck the logs for more information");
            }

            if (!$scaffold->createResource((bool)$this->option('resource'))) {
                $this->error("Resource creation failed \nCheck the logs for more information");
            }

            if (!$scaffold->createService((bool)$this->option('service'))) {
                $this->error("Service creation failed \nCheck the logs for more information");
            }
            $this->info("Module scaffolded successfully");
           
        } else {
            $this->error("Model scaffold failed \nCheck the logs for more information");
        }

    }
}