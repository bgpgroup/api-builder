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
                            {--group= : Namespace of the class}
                            {--migration= : y/n default y}
                            ';
    // php artisan bgp:make:crud sales --fields=int:id,string:name,string:description,float:price --rules='name=required;description=nullable;price=numeric' --columns='string:name,50;text:description|nullable' --group='Orders/Sales'


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
        $model = Str::studly(Str::singular($name));
        $routeFile = base_path('routes/api.php');
        $routeName = Str::snake($name, '-');
        $tableName = Str::snake($name, '_');
        $group = $this->option('group');

        $modelName = $this->getModelName($model, false);
        $dTObjectName = $this->getDTObjectName($model, false);
        $collectionName = $this->getCollectionName($model, false);
        
        // $this->call('bgp:make:resource', ['name' => $model . 'Resource', '--fields' => $fields]);
        // $this->call('bgp:make:collection', ['name' => $model . 'Collection']);

        // php artisan bgp:make:model Parties/People/Models/Holaw --fields=name,description
        $this->call('bgp:make:model', [
            'name' => $modelName,
            '--fields' => $this->getModelFields(),
        ]);        
        
        // php artisan bgp:make:dto Parties/People/DTObjects/OrderDTObject --model=Domain/Parties/People/Models/Order --fields=int:id,string:name,string:description,float:price
        $this->call('bgp:make:dto', [
            'name' => $dTObjectName, 
            '--model' => $this->getModelName($model), 
            '--fields' => $fields
        ]);
        
        // php artisan bgp:make:collection-dto Parties/Companies/Collections/BallCollection --model=Domain/Parties/People/Models/Ball --dto=Domain/Parties/Companies/DTObjects/BallDTObject
        $this->call('bgp:make:collection-dto', [
            'name' => $this->getCollectionName($model, false),
            '--model' => $this->getModelName($model),
            '--dto' => $this->getDTObjectName($model)
        ]);
        
        // php artisan bgp:make:request Parties/People/Requests/TableRequest --rules='name=required|max:50;description=nullable'
        $this->call('bgp:make:request', [
            'name' => $this->getRequestName($model, false),
            '--rules' => $rules
        ]);
        
        // php artisan bgp:make:controller-dto BallController --group='Parties/People'
        $this->call('bgp:make:controller-dto', [
            'name' => $model . 'Controller',
            '--group' => $group
        ]);

        if ($this->option('migration', 'y') == 'n') {
            // php artisan bgp:make:migration invoices --columns='string:name,50;text:description|nullable'
            $this->call('bgp:make:migration', [
                'name' => $tableName,
                '--columns' => $columns
            ]);
        }
        
        // php artisan bgp:make:factory BookFactory --columns='string:name;text:description;foreign:author_id' --group='Parties/People'
        $this->call('bgp:make:factory', [
            'name' => $model . 'Factory',
            '--columns' => $columns,
            '--group' => $group
        ]);
        
        // php artisan bgp:make:test Phone --group='Parties/People'
        $this->call('bgp:make:test', [
            'name' => $model,
            '--group' => $group
        ]);

        File::append($routeFile, "\n" . implode("\n", $this->addRoutes($routeName, $model . 'Controller')));

        if ($tests == 'yes') {
            //\Artisan::call('test --filter=' . $model . 'Test');
        }
    }

    protected function getModelName($name, $useBase = true)
    {
        return ($useBase ? config('api-builder.domain_path') . '/' : '') . $this->option('group') . '/' . 'Models' . '/' . $name;
    }

    protected function getDTObjectName($name, $useBase = true)
    {
        return ($useBase ? config('api-builder.domain_path') . '/' : '') . $this->option('group') . '/' . 'DTObjects' . '/' . $name . 'DTObject';
    }

    protected function getCollectionName($name, $useBase = true)
    {
        return ($useBase ? config('api-builder.domain_path') . '/' : '') . $this->option('group') . '/' . 'Collections' . '/' . $name . 'Collection';
    }

    protected function getRequestName($name, $useBase = true)
    {
        return ($useBase ? config('api-builder.core_path') . '/' : '') . $this->option('group') . '/' . 'Requests' . '/' . $name . 'Request';
    }

    /**
     * Add routes.
     *
     * @return  array
     */
    protected function addRoutes($routeName, $controllerName)
    {
        return ["Route::apiResource('" . $routeName . "', \\" . config('api-builder.core_path') . "\\" . str_replace('/', "\\", $this->option('group')). "\\" . 'Controllers' . "\\" . $controllerName . "::class);"];
    }

    protected function getModelFields()
    {
        $fields = rtrim($this->option('fields'), ',');

        $fielddsArr = explode(',', $fields);

        $result = [];

        foreach ($fielddsArr as $f) {
            $name = explode(':', $f);
            if (isset($name[1])) {
                $result[] = $name[1];
            }
        }

        return implode(',', $result);
    }

}