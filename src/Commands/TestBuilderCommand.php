<?php

namespace BgpGroup\ApiBuilder\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Exception\InvalidArgumentException;

class TestBuilderCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bgp:make:test
                            {name} : The name of the module
                            {--module= : The moule name}
                            ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate test';

    protected $type = 'Test';

    /**
     *
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/../stubs/' . '/' . 'test.stub';
    }

    protected function getPath($name)
    {
        return 'src/Modules/' . Str::studly($this->option('module')) . '/Tests/Feature/' . Str::studly($this->argument('name')) . 'Test.php';
    }

    protected function endpoint()
    {
        return config(Str::lower($this->option('module')) . '.resources.' . Str::studly($this->argument('name')) . '.endpoint');
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
            throw new InvalidArgumentException("Missing required argument name");
        }

        if(!$this->option('module')){
            throw new InvalidArgumentException("Missing required argument module");
        }

        $stub = str_replace('$ENDPOINT$', $this->endpoint(), $stub);

        $stub = str_replace('$CAMEL_MODULE_NAME$', Str::studly($this->option('module')), $stub);

        $stub = str_replace('$CAMEL_RESOURCE_NAME$', Str::studly($this->argument('name')), $stub);

        $stub = str_replace('$RESOURCE_NAME$', Str::camel($this->argument('name')), $stub);

        $stub = str_replace('$SNAKE_PLURAL_RESOURCE_NAME$', Str::plural(Str::snake($this->argument('name'))), $stub);

        return str_replace('$SNAKE_SINGULAR_RESOURCE_NAME$',  Str::singular(Str::snake($this->argument('name'))), $stub);
    }


}
