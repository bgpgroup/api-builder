<?php

namespace BgpGroup\ApiBuilder\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Exception\InvalidArgumentException;

class ApiCollectionDTOBuilderCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bgp:make:collection-dto 
                            {name} : The name of the collection
                            {--dto= : The list of fields separated by comma.}
                            {--model= : The model name with namespace.}
                            ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Collection DTO';

    protected $type = 'Collection DTO';

    /**
     * 
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/../stubs/' . '/' . 'dto-collection.stub';
    }

    protected function getDefaultNamespace($rootnamespace)
    {
        return 'App';
    }

    protected function getPath($name)
    {
        $namespace = str_replace("App\\", '', config('api-builder.dto-collection.namespace'));
        $namespace = str_replace("\\", '/', $namespace) . '/';
        $basePath = config('api-builder.dto-collection.base');
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
            throw new InvalidArgumentException("Missing required argument dto name");
        }

        $stub = $this->setNamespace($stub, $namespace);

        $stub = $this->replaceModelClass($stub, $this->option('model'));

        $stub = $this->replaceDtoClass($stub, $this->option('dto'));

        $stub = $this->replaceExtends($stub, config('api-builder.dto-collection.extends'));

        return $this->replaceClassName($stub, $classname);
    }

    protected function getNamespace($name)
    {
        $name = str_replace("App\\", '', $name);

        $nameArray = explode("\\", $name);

        $classname = array_pop($nameArray);

        return config('api-builder.dto-collection.namespace') . (count($nameArray) > 0  ? "\\" . implode("\\", $nameArray) : '');
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
        return str_replace('DummyClassName', $name, $stub);
    }

    protected function replaceModelClass($stub, $name)
    {
        $name = str_replace("/", "\\", $name);

        $stub = str_replace('DummyModelUse', $name, $stub);

        $nameArray = explode("\\", $name);

        $modelName = end($nameArray);

        return str_replace('DummyModelName', $modelName, $stub);
    }

    protected function replaceDtoClass($stub, $name)
    {
        $name = str_replace("/", "\\", $name);

        $stub = str_replace('DummyDtoUse', $name, $stub);

        $nameArray = explode("\\", $name);

        $modelName = end($nameArray);

        return str_replace('DummyDtoName', $modelName, $stub);
    }

    protected function replaceExtends($stub, $name)
    {
        $stub = str_replace('DummyExtendsUse', $name, $stub);

        $extendsArray = explode('\\', $name);
        $extendsClass = end($extendsArray);

        return str_replace('DummyExtendsName', $extendsClass, $stub);
    }
}