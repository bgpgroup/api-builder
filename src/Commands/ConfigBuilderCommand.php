<?php

namespace BgpGroup\ApiBuilder\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Exception\InvalidArgumentException;

class ConfigBuilderCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bgp:make:config 
                            {name} : The name of the module
                            ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Module Config';

    protected $type = 'Config';

    /**
     * 
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/../stubs/' . '/' . 'config.stub';
    }

    protected function getPath($name)
    {
        return 'src/Modules/' . Str::studly($this->argument('name')) . '/config/' . Str::lower($this->argument('name')) . '.php';
    }
}