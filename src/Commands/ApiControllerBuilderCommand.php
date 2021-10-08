<?php

namespace BgpGroup\ApiBuilder\Commands;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Exception\InvalidArgumentException;

class ApiControllerBuilderCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bgp:make:controller 
                            {name} : The name of the controller
                            ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Controller';

    /**
     * 
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/../stubs/' . '/' . 'controller.stub';
    }

    protected function getDefaultNamespace($rootnamespace)
    {
        return config('api-builder.controllers.namespace', $rootnamespace . '\Http\Controllers');
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
        if(!$this->argument('name')){
            throw new InvalidArgumentException("Missing required argument controller name");
        }

        return $this->replaceClassName($stub, $this->argument('name'));
    }

    protected function replaceClassName($stub, $name)
    {
        $dummyName = str_replace('Controller', '', $name);
        return str_replace('Dummy', $dummyName, $stub);
    }

}