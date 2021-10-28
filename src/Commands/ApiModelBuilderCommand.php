<?php

namespace BgpGroup\ApiBuilder\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Exception\InvalidArgumentException;

class ApiModelBuilderCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bgp:make:model 
                            {name} : The name of the model
                            {--fields= : The list of fields separated by comma.}
                            ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate api model';

    protected $type = 'Model';

     /**
     * Execute the console command.
     *
     * @return bool|null
     */
    // public function fire(){

    //     $this->setModelClass();
    // }

    /**
     * 
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/../stubs/' . '/' . 'model.stub';
    }

    protected function getDefaultNamespace($rootnamespace)
    {
        //return $rootnamespace . '\Models';
        return 'App';
    }

    protected function getPath($name)
    {
        $namespace = str_replace("App\\", '', config('api-builder.models.namespace'));
        $namespace = str_replace("\\", '/', $namespace) . '/';
        $basePath = config('api-builder.models.base');
        $base = Str::endsWith($basePath, '/') ? $basePath : $basePath . '/';
        return $basePath . $namespace . $this->argument('name') . '.php';
    }

    /**
     * Replace the class name for the given stub.
     *
     * @param  string  $stub
     * @param  string  $name
     * @return string
     */
    protected function replaceClass($stub, $name)
    {
        $namespace = $this->getNamespace($name);
        $classname = $this->getClass($name);

        if(!$this->argument('name')){
            throw new InvalidArgumentException("Missing required argument model name");
        }

        $fields = rtrim($this->option('fields'), ',');

        $stub = parent::replaceClass($stub, $classname);

        $stub = $this->setNamespace($stub, $namespace);

        $stub = $this->replaceClassName($stub, $classname);

        $stub = $this->replaceFillableFields($stub, $this->getFilableFields($fields));

        return  $this->replaceExtends($stub, config('api-builder.models.extends'));

    }

    protected function getNamespace($name)
    {
        $name = str_replace("App\\", '', $name);

        $nameArray = explode("\\", $name);

        $classname = array_pop($nameArray);

        return config('api-builder.models.namespace') . (count($nameArray) > 0  ? "\\" . implode("\\", $nameArray) : '');
    }

    protected function getClass($name)
    {
        $nameArray = explode("\\", $name);

        return end($nameArray);
    }

    protected function setNamespace($stub, $namespace)
    {
        return str_replace('DummyNamespace', $namespace, $stub);
    }

    protected function replaceClassName($stub, $name)
    {
        return str_replace('DummyModel', $name, $stub);
    }

    protected function replaceExtends($stub, $name)
    {
        $stub = str_replace('DummyExtendsPath', $name, $stub);

        $extendsArray = explode('\\', $name);
        $extendsClass = end($extendsArray);

        return str_replace('DummyExtendsName', $extendsClass, $stub);
    }

    protected function replaceFillableFields($stub, $fields)
    {
        return str_replace('{{fillableFields}}', $fields, $stub);
    }

    protected function getFilableFields($fields)
    {
        $fillableFields = '';
        if (trim($fields) != '') {

            $fields = explode(',', $fields);
            foreach ($fields as $field) {
                if (trim($field) == '') {
                    continue;
                }
                $fillableFields .= "\n\t\t'$field',";
            }
        }

        return $fillableFields;
    }
}