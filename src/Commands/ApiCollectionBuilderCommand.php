<?php

namespace BgpGroup\ApiBuilder\Commands;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Exception\InvalidArgumentException;

class ApiCollectionBuilderCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bgp:make:collection 
                            {name} : The name of the collection

                            ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Collection';

    /**
     * 
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/../stubs/' . '/' . 'Collection.stub';
    }

    protected function getDefaultNamespace($rootnamespace)
    {
        return $rootnamespace . '\Http\Resources';
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
            throw new InvalidArgumentException("Missing required argument collection name");
        }

        return $this->replaceClassName($stub, $this->argument('name'));
    }

    protected function replaceClassName($stub, $name)
    {
        return str_replace('DummyCollection', $name, $stub);
    }

}