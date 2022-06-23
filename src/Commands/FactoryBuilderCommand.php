<?php

namespace BgpGroup\ApiBuilder\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Exception\InvalidArgumentException;

class FactoryBuilderCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bgp:make:factory
                            {name} : The name of the resource
                            {--module= : The moule name}
                            ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate factory';

    protected $type = 'Factory';

    /**
     *
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/../stubs/' . '/' . 'factory.stub';
    }

    protected function definitions()
    {
        return config(Str::lower($this->option('module')) . '.resources.' . Str::studly($this->argument('name')) . '.factory.fields');
    }

    protected function getPath($name)
    {
        return 'src/Modules/' . Str::studly($this->option('module')) . '/Factories/' . Str::studly($this->argument('name')) . 'Factory.php';
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

        $stub = str_replace('$CAMEL_MODULE_NAME$', Str::studly($this->option('module')), $stub);

        $stub = str_replace('$CAMEL_RESOURCE_NAME$', Str::studly($this->argument('name')), $stub);

        return str_replace('$FIELDS_DEFINITION$', $this->getFieldsDefinition(), $stub);
    }

    protected function getFieldsDefinition()
    {
        $definitions = $this->definitions();

        $result = '';

        foreach ($definitions as $name => $definition) {
            $result .= "\n            '$name' => $definition,";
        }

        return $result;
    }
}
