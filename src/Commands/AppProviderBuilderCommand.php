<?php

namespace BgpGroup\ApiBuilder\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Exception\InvalidArgumentException;

class AppProviderBuilderCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bgp:make:app-provider 
                            {name} : The name of the module
                            ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate App Provider';

    protected $type = 'Provider';

    /**
     * 
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/../stubs/' . '/' . 'app-service-provider.stub';
    }

    // protected function getDefaultNamespace($rootnamespace)
    // {
    //     return $rootnamespace . '\Http\Resources';
    // }

    protected function getPath($name)
    {
        return 'src/Modules/' . Str::studly($this->argument('name')) . '/Providers/AppServiceProvider.php';
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

        $stub = str_replace('$LOWER_MODULE_NAME$', Str::lower($this->argument('name')), $stub);

        return str_replace('$CAMEL_MODULE_NAME$', Str::studly($this->argument('name')), $stub);
    }
}