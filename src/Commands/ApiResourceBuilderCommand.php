<?php

namespace BgpGroup\ApiBuilder\Commands;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Exception\InvalidArgumentException;

class ApiResourceBuilderCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bgp:make:resource 
                            {name} : The name of the resource
                            {--fields= : The list of fields separated by comma.}
                            ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Resource';

    protected $type = 'Resource';

    /**
     * 
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/../stubs/' . '/' . 'resource.stub';
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
            throw new InvalidArgumentException("Missing required argument resource name");
        }

        $fields = rtrim($this->option('fields'), ',');

        $stub = $this->replaceFields($stub, $this->getFields($fields));

        return $this->replaceClassName($stub, $this->argument('name'));
    }

    protected function replaceClassName($stub, $name)
    {
        return str_replace('DummyResource', $name, $stub);
    }

    protected function getFields($fields)
    {
        $result = '';
        if (trim($fields) != '') {

            $fields = explode(',', $fields);
            foreach ($fields as $field) {
                if (trim($field) == '') {
                    continue;
                }
                $result .= "\n\t\t\t'$field' => \$this->$field,";
            }
        }

        return $result;
    }

    protected function replaceFields($stub, $fields)
    {
        return str_replace('{{fields}}', $fields, $stub);
    }

}