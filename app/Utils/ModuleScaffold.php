<?php

namespace App\Utils;


use App\Traits\Common;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * module generator
 */
class ModuleScaffold
{
    use Common;

    protected string $module;
    protected string $model;
    protected bool $migration;
    protected string $path;
    protected string $moduleVersion = 'v0';
    protected string $namespace;


    /**
     * scaffold a new module
     *
     * @param string $module
     * @param string|null $model
     * @param bool $migration
     */
    public function __construct(string $module, string $model = null, bool $migration = false)
    {
        $this->module = ucfirst($module);
        $this->model = $model ? ucfirst($model) : null;
        $this->migration = $migration;

        $this->path = base_path() . '/app/Modules/' . $this->moduleVersion . '/' . $module;
        $this->namespace = 'namespace App\Modules\\' . $this->moduleVersion . '\\' . $this->module;
    }

    /**
     * generate the module scaffold
     *
     * @return bool
     */
    public function generate(): bool
    {
        $this->createModule();

        if ($this->createModel()) {
            $this->createInterface();
            $this->createRepository();
            $this->createResource();
            $this->createService();
            $this->createEnum();

            return true;
        }

        return false;
    }

    /**
     * crate a module
     *
     * @return bool
     */
    public function createModule(): bool
    {
        //check if root module folder exists if not create it
        $rootModulePath = base_path() . '/app/Modules';

        if (!self::createFolder($rootModulePath)) {
            return false;
        }

        return self::createFolder($this->path);
    }

    /**
     * create a model
     *
     * @return bool
     */
    public function createModel(): bool
    {
        $modelCreated = false;
        //check if module models folder exists if not create it
        $modelPath = $this->path . '/Models';
        if (!self::createFolder($modelPath)) {
            return false;
        }

        $eloquent = "\n\nuse " . Model::class . ";\n";
        $modelClass = "\nclass " . $this->model . " extends Model";
        $hasFactory = "use " . HasFactory::class . ";";
        $useFactory = "use HasFactory;";

        $filename = $this->path . '/Models/' . $this->model . '.php';

        if (!is_file($filename)) {
            $file = fopen($filename, 'w');
            fwrite($file, "<?php\n\n");
            fwrite($file, $this->namespace . '\Models;');
            fwrite($file, $eloquent);
            fwrite($file, $hasFactory);
            fwrite($file, "\n\n");
            fwrite($file, $modelClass);
            fwrite($file, "\n{\n\t" . $useFactory . "\n}");
            fclose($file);

            $modelCreated = true;
        }

        if ($modelCreated && $this->migration && config('app.env') !== 'testing') {
            $migrationName = strtolower(Str::plural(strtolower($this->model)));
            Artisan::call('make:migration', ['name' => 'create_' . $migrationName . '_table']);
        }

        return $modelCreated;
    }


    /**
     * create an interface
     *
     * @param bool $hasInterface
     * @return bool
     */
    public function createInterface(bool $hasInterface = true): bool
    {
        $interfaceCreated = false;
        if (!$hasInterface) {
            return true;
        }
        try {

            $interfacePath = $this->path . '/Interfaces';
            if (!self::createFolder($interfacePath)) {
                Log::alert('__METHOD__: ' . __METHOD__ . ': ' . 'failed to create interface folder');
                return false;
            }

            $interface = "\n\ninterface " . $this->model . "Interface";
            $filename = $this->path . '/Interfaces/' . $this->model . 'Interface.php';

            if (!is_file($filename)) {
                $file = fopen($filename, 'w');
                fwrite($file, "<?php\n\n");
                fwrite($file, $this->namespace . "\Interfaces;\n\n");
                fwrite($file, $interface);
                fwrite($file, "\n{\n\t\n}");
                fclose($file);

                $interfaceCreated = true;
            }
        } catch (Exception $ex) {
            self::logError($ex, __CLASS__);
        }

        return $interfaceCreated;
    }

    /**
     * create repository
     *
     * @param bool $hasRepository
     * @return bool
     */
    public function createRepository(bool $hasRepository = true): bool
    {
        $repoCreated = false;
        if (!$hasRepository) {
            return true;
        }

        try {
            $repoPath = $this->path . '/Repositories';
            if (!self::createFolder($repoPath)) {
                Log::alert('__METHOD__: ' . __METHOD__ . ': ' . 'failed to create repository folder');
                return false;
            }

            $importInterface = "\n\nuse App\Modules\\" . $this->moduleVersion . "\\" . $this->module . "\\Interfaces\\" . $this->model . "Interface;";
            $baseRepository = "\nuse App\Modules\BaseRepository;";
            $importModel = "\nuse App\Modules\\" . $this->moduleVersion . "\\" . $this->module . "\Models\\" . $this->model . ";";
            $repoClass = "class " . $this->model . "Repository extends BaseRepository implements " . $this->model . "Interface";
            $constructor = "\n\tpublic function __construct(){\n\t\t parent::__construct(new " . $this->model . "());\n\t}";

            $filename = $this->path . '/Repositories/' . $this->model . 'Repository.php';
            if (!is_file($filename)) {
                $file = fopen($filename, 'w');
                fwrite($file, "<?php\n\n");
                fwrite($file, $this->namespace . '\Repositories;');
                fwrite($file, $importInterface);
                fwrite($file, $baseRepository);
                fwrite($file, $importModel);
                fwrite($file, "\n\n" . $repoClass . "\n{\n");
                fwrite($file, $constructor . "\n}");
                fclose($file);

                $repoCreated = true;
            }
        } catch (Exception $ex) {
            self::logError($ex, __CLASS__);
        }

        return $repoCreated;
    }

    /**
     * create api resource file
     *
     * @param bool $hasResource
     * @return bool
     */
    public function createResource(bool $hasResource = true): bool
    {
        $resourceCreated = false;
        if (!$hasResource) {
            return true;
        }

        try {
            $resourcePath = $this->path . "/Resources";
            if (!self::createFolder($resourcePath)) {
                Log::alert('__METHOD__: ' . __METHOD__ . ': ' . 'failed to create resource folder');
                return false;
            }

            $importResource = "\nuse " . JsonResource::class . ';';
            $resourceClass = "\n\nclass " . $this->model . "Resource extends JsonResource{\n";
            $comment = "\t/**\n\t* Transform the resource into an array. \n\t*\n\t* @param \Illuminate\Http\Request \$request \n\t*\n\t                     @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable\n\t*/";
            $toArray = "\n\tpublic function toArray(\$request)\n\t{\n\t\treturn parent::toArray(\$request);\n\t}";

            $filename = $this->path . '/Resources/' . $this->model . 'Resource.php';
            if (!is_file($filename)) {
                $file = fopen($filename, 'w');
                fwrite($file, "<?php \n\n");
                fwrite($file, $this->namespace . '\Resources;');
                fwrite($file, $importResource);
                fwrite($file, $resourceClass);
                fwrite($file, $comment);
                fwrite($file, $toArray . "\n}");
                fclose($file);

                $resourceCreated = true;
            }

        } catch (Exception $ex) {
            self::logError($ex);
        }

        return $resourceCreated;
    }

    /**
     * create a service
     *
     * @param bool $hasService
     * @return bool
     */
    public function createService(bool $hasService = true): bool
    {
        $serviceCreated = false;
        if (!$hasService) {
            return true;
        }

        try {
            $servicePath = $this->path . '/Services';
            if (!self::createFolder($servicePath)) {
                Log::alert('__METHOD__: ' . __METHOD__ . ': ' . 'failed to create services folder');
                return false;
            }

            $importRepository = "\n\nuse App\Modules\\" . $this->moduleVersion . "\\" . $this->module . "\\Repositories\\" . $this->model . "Repository;";
            $baseService = "\nuse App\Modules\BaseService;";
            $serviceClass = "class " . $this->model . 'Service extends BaseService';
            $repoType = $this->model . 'Repository';
            $constructor = "\n\tpublic function __construct({$repoType} \$repository){\n\t\t\$this->repository = \$repository;\n\t}";

            $filename = $this->path . '/Services/' . $this->model . 'Service.php';

            if (strpos($this->model, '/') !== false) {
                $parts = explode('/', $this->model);
                $directory = $this->path . '/Services/' . $parts[0];
                $this->namespace = $this->namespace . '\\' . $parts[0];
                $serviceClass = "class " . $parts[1] . 'Service extends BaseService';

                if (!is_dir($directory)) {
                    if (!mkdir($directory, 0777, true)) {
                        die("Failed to create the directory: $directory");
                    }
                }
            }

            if (!is_file($filename)) {


                $file = fopen($filename, 'w');
                \Log::info('this got here', [$filename]);
                fwrite($file, "<?php\n\n");
                fwrite($file, $this->namespace . '\Services;');
                fwrite($file, $importRepository);
                fwrite($file, $baseService);
                fwrite($file, "\n\n" . $serviceClass . "\n{\n");
                fwrite($file, $constructor . "\n}");
                fclose($file);

                $serviceCreated = true;
            }
        } catch (Exception $ex) {
            self::logError($ex);
        }

        return $serviceCreated;
    }

    /**
     * create a service
     *
     * @param bool $hasService
     * @return bool
     */
    public function createEnum(bool $hasEnum = true): bool
    {
        $enumCreated = false;
        if (!$hasEnum) {
            return true;
        }
        try {
            $enumPath = $this->path . '/Enums';
            if (!self::createFolder($enumPath)) {
                Log::alert('__METHOD__: ' . __METHOD__ . ': ' . 'failed to create enums folder');
                return false;
            }
            $enum = "enum {$this->model}Enum";
            $filename = $this->path . '/Enums/' . $this->model . 'Enum.php';
            if (!is_file($filename)) {
                $file = fopen($filename, 'w');
                fwrite($file, "<?php\n\n");
                fwrite($file, $this->namespace . '\Enums;');
                fwrite($file, "\n\n" . $enum . "\n{\n");
                fwrite($file, "\n}");
                fclose($file);
                $enumCreated = true;
            }
        } catch (Exception $ex) {
            self::logError($ex);
        }
        return $enumCreated;
    }

}
