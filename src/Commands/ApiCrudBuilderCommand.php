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
                            ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Crud';

    public function handle()
    {
        $name = $this->argument('name');
        $rules = rtrim($this->option('rules'), ';');
        $fields = rtrim($this->option('fields'), ',');
        $modelName = Str::studly(Str::singular($name));
        $routeFile = base_path('routes/api.php');
        $routeName = Str::snake($name, '-');

        $this->call('bgp:make:model', ['name' => $modelName, '--fields' => $fields]);
        $this->call('bgp:make:resource', ['name' => $modelName . 'Resource', '--fields' => $fields]);
        $this->call('bgp:make:collection', ['name' => $modelName . 'Collection']);
        $this->call('bgp:make:request', ['name' => $modelName . 'Request', '--rules' => $rules]);
        $this->call('bgp:make:controller', ['name' => $modelName . 'Controller']);

        File::append($routeFile, "\n" . implode("\n", $this->addRoutes($routeName, $modelName . 'Controller')));
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