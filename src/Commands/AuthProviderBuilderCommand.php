<?php

namespace BgpGroup\ApiBuilder\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Exception\InvalidArgumentException;

class AuthProviderBuilderCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bgp:make:auth-provider 
                            {name} : The name of the module
                            ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Auth Provider';

    protected $type = 'Provider';

    /**
     * 
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/../stubs/' . '/' . 'auth-service-provider.stub';
    }

    // protected function getDefaultNamespace($rootnamespace)
    // {
    //     return $rootnamespace . '\Http\Resources';
    // }

    protected function getPath($name)
    {
        return 'src/Modules/' . Str::studly($this->argument('name')) . '/Providers/AuthServiceProvider.php';
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
            throw new InvalidArgumentException("Missing required argument module");
        }

        return str_replace('$CAMEL_MODULE_NAME$', Str::studly($this->argument('name')), $stub);
    }
}