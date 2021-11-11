<?php

namespace BgpGroup\ApiBuilder\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Exception\InvalidArgumentException;

class ApiTestBuilderCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bgp:make:test 
                            {name} : The name of the test
                            {--group= : The group.}
                            ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Test';

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
        $name = str_replace($this->laravel->getNamespace(), '', $name);
        return base_path('tests/Feature/') . $this->option('group') . '/' . $name . 'Test.php';
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
        $name = str_replace("App\\", '', $name);
        $namespace = $this->getNamespace($name);

        if(!$this->argument('name')){
            throw new InvalidArgumentException("Missing required argument test name");
        }

        $stub = $this->setNamespace($stub, $namespace);
        $stub = $this->replaceModelClass($stub, $this->argument('name'));
        $stub = $this->replaceClassName($stub, $this->argument('name'));
        $stub = $this->replacePluralName($stub, $this->argument('name'));
        $stub = $this->replaceBaseEndpoint($stub);
        $stub = $this->replaceEndpointName($stub, $this->argument('name'));
        $stub = $this->replaceMakeHidden($stub);

        return $this->replaceSingularName($stub, $this->argument('name'));
    }

    protected function replaceClassName($stub, $name)
    {
        //$dummyName = str_replace('Controller', '', $name);
        return str_replace('Dummy', $name, $stub);
    }

    protected function replaceSingularName($stub, $name)
    {
        $name = Str::snake($name);
        return str_replace('{{singular_model}}', $name, $stub);
    }

    protected function replacePluralName($stub, $name)
    {
        $name = Str::plural(Str::snake($name));
        return str_replace('{{plural_model}}', $name, $stub);
    }

    protected function replaceBaseEndpoint($stub)
    {
        return str_replace('{{base_endpoint}}', config('api-builder.base_api'), $stub);
    }

    protected function replaceEndpointName($stub, $name)
    {
        $name = Str::plural(Str::snake($name, '-'));
        return str_replace('{{endpoint_name}}', $name, $stub);
    }

    protected function replaceMakeHidden($stub)
    {
        $text = "->makeHidden(['" . implode("','", config('api-builder.tests.hide_fields')) . "'])";
        return str_replace('{{makeHidden}}', $text, $stub);
    }

    protected function replaceModelClass($stub, $name)
    {
        $name = str_replace('Test', '', $name);
        
        $group = str_replace("/", "\\", $this->option('group')) ;

        $name = 'Domain' . "\\" . $group .  "\\" . "Models" . "\\" . $name;

        $name = str_replace("/", "\\", $name);

        return str_replace('DummyModelUse', $name, $stub);
    }

    protected function getNamespace($name)
    {
        return $this->getDomainPath();
    }

    protected function getDomainPath($type = '')
    {
        $group = str_replace("/", "\\", $this->option('group')) ;

        return 'Tests\Feature' . "\\" . $group .  (!empty($type) ? ("\\" . $type) : '');
    }

    protected function setNamespace($stub, $namespace)
    {
        return str_replace('DummyNamespace', $namespace, $stub);
    }
}