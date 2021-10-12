<?php

namespace BgpGroup\ApiBuilder\Commands;

use File;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;

class ApiCrudBuilderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bgp:make:crud 
                            {name} : The name of the crud
                            {--fields= : The fields list.}
                            {--rules= : The list of validations rules.}
                            {--columns= : The list of types of colums.}
                            {--tests= : If this option exists, it run the tests created.}
                            ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Crud';

    protected $type = 'CRUD';

    public function handle()
    {
        $name = $this->argument('name');
        $rules = rtrim($this->option('rules'), ';');
        $fields = rtrim($this->option('fields'), ',');
        $columns = rtrim($this->option('columns'), ';');
        $tests = rtrim($this->option('tests'), ';');
        $modelName = Str::studly(Str::singular($name));
        $routeFile = base_path('routes/api.php');
        $routeName = Str::snake($name, '-');
        $tableName = Str::snake($name, '_');

        $this->call('bgp:make:model', ['name' => $modelName, '--fields' => $fields]);
        $this->call('bgp:make:resource', ['name' => $modelName . 'Resource', '--fields' => $fields]);
        $this->call('bgp:make:collection', ['name' => $modelName . 'Collection']);
        $this->call('bgp:make:request', ['name' => $modelName . 'Request', '--rules' => $rules]);
        $this->call('bgp:make:controller', ['name' => $modelName . 'Controller']);
        $this->call('bgp:make:migration', ['name' => $tableName, '--columns' => $columns]);
        $this->call('bgp:make:factory', ['name' => $modelName . 'Factory', '--columns' => $columns]);
        $this->call('bgp:make:test', ['name' => $modelName]);

        File::append($routeFile, "\n" . implode("\n", $this->addRoutes($routeName, $modelName . 'Controller')));

        if ($tests == 'yes') {
            //\Artisan::call('test --filter=' . $modelName . 'Test');
        }
    }

    /**
     * Add routes.
     *
     * @return  array
     */
    protected function addRoutes($routeName, $controllerName)
    {
        return ["Route::apiResource('" . $routeName . "', \\" . config('api-builder.controllers.namespace',  'App\Http\Controllers') . "\\" . $controllerName . "::class);"];
    }

}