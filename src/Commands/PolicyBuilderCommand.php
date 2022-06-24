<?php

namespace BgpGroup\ApiBuilder\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Exception\InvalidArgumentException;

class PolicyBuilderCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bgp:make:policy
                            {name} : The name of the module
                            {--module= : The moule name}
                            ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate policy';

    protected $type = 'Policy';

    /**
     *
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/../stubs/' . '/' . 'policy.stub';
    }

    protected function getPath($name)
    {
        return 'src/Modules/' . Str::studly($this->option('module')) . '/Policies/' . Str::studly($this->argument('name')) . 'Policy.php';
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

        $stub = str_replace('$CAMEL_RESOURCE$', Str::camel($this->argument('name')), $stub);

        $stub = str_replace('$STUDLY_RESOURCE$', Str::studly($this->argument('name')), $stub);

        $stub = str_replace('$STUDLY_MODULE$', Str::studly($this->option('module')), $stub);

        $stub = str_replace('$PLURAL_SNAKE_RESOURCE$', Str::plural(Str::lower(Str::snake($this->argument('name'), '-'))), $stub);

        $stub = str_replace('$SNAKE_MODULE$', Str::lower(Str::snake($this->option('module'), '-')), $stub);

        return str_replace('$SPACE_RESOURCE$', Str::lower(Str::snake($this->argument('name'), ' ')), $stub);
    }
}
