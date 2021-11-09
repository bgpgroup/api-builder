<?php

namespace BgpGroup\ApiBuilder\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Exception\InvalidArgumentException;

class ApiControllerDTOBuilderCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bgp:make:controller-dto
                            {name} : The name of the controller
                            {--group= : The group.}
                            ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Controller';

    protected $type = 'Controller with DTO';

    /**
     * 
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/../stubs/' . '/' . 'dto-controller.stub';
    }

    protected function getDefaultNamespace($rootnamespace)
    {
        return 'App';
    }

    protected function getPath($name)
    {
        //$namespace = str_replace("App\\", '', config('api-builder.dto-controller.namespace'));
        //$namespace = str_replace("\\", '/', $namespace)     ;
        $basePath = config('api-builder.controllers.base');
        $base = str_replace("\\", "/", $name); 
        $base = Str::endsWith($basePath, '/') ? $basePath : $basePath . '/';
        $corePath = str_replace("\\", "/", config('api-builder.core_path'));
        return $base . '/' . $corePath . '/' . $this->option('group') . '/Controllers/' . $this->argument('name') . '.php';
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
            throw new InvalidArgumentException("Missing required argument controller name");
        }

        $classname = str_replace('Controller', '', $classname);
        $stub = $this->setNamespace($stub, $namespace);
        $stub = $this->replaceModelClass($stub, $classname);
        $stub = $this->replaceDtoClass($stub, $classname);
        $stub = $this->replaceRequestClass($stub, $classname);
        $stub = $this->replaceCollectionClass($stub, $classname);

        $stub = $this->replaceModelName($stub, $classname);
        return $this->replaceClassName($stub, $classname);
    }

    protected function getNamespace($name)
    {
        return $this->getCorePath('Controllers');
    }

    protected function getClass($name)
    {
        $nameArray = explode("\\", $name);

        return end($nameArray);
    }

    protected function getDomainPath($type = '')
    {
        $group = str_replace("/", "\\", $this->option('group')) ;

        return config('api-builder.domain_path') . "\\" . $group .  (!empty($type) ? ("\\" . $type) : '');
    }

    protected function getCorePath($type = '')
    {
        $group = str_replace("/", "\\", $this->option('group')) ;

        return config('api-builder.core_path') . "\\" . $group . (!empty($type) ? ("\\" . $type) : '');
    }

    protected function replaceClassName($stub, $name)
    {
        //$dummyName = str_replace('Controller', '', $name);
        return str_replace('Dummy', $name, $stub);
    }

    protected function replaceModelName($stub, $name)
    {
        $modelName = Str::camel($name);
        return str_replace('{{model}}', "$" . $modelName, $stub);
    }

    protected function replaceModelClass($stub, $name)
    {
        $name = str_replace("/", "\\", $name);

        $name = $this->getDomainPath('Models') . "\\". $name;

        return str_replace('DummyModelUse', $name, $stub);
    }

    protected function replaceDtoClass($stub, $name)
    {
        $name = str_replace("/", "\\", $name);

        $name = $this->getDomainPath('DTObjects') . "\\". $name . 'DTObject';

        return str_replace('DummyDTObjectUse', $name, $stub);
    }

    protected function replaceRequestClass($stub, $name)
    {
        $name = str_replace("/", "\\", $name);

        $name = $this->getCorePath('Requests') . "\\". $name . 'Request';

        return str_replace('DummyRequestUse', $name, $stub);
    }

    protected function replaceCollectionClass($stub, $name)
    {
        $name = str_replace("/", "\\", $name);

        $name = $this->getDomainPath('Collections') . "\\". $name . 'Collection';

        return str_replace('DummyCollectionUse', $name, $stub);
    }

    protected function setNamespace($stub, $namespace)
    {
        return str_replace('DummyNamespace', $namespace, $stub);
    }

}