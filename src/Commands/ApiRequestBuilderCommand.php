<?php

namespace BgpGroup\ApiBuilder\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Exception\InvalidArgumentException;

class ApiRequestBuilderCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bgp:make:request 
                            {name} : The name of the Request
                            {--rules= : The list of validations.}
                            ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Request';

    protected $type = 'Request';

    /**
     * 
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/../stubs/' . '/' . 'request.stub';
    }

    protected function getDefaultNamespace($rootnamespace)
    {
        return 'App';
    }

    protected function getPath($name)
    {
        $namespace = str_replace("App\\", '', config('api-builder.requests.namespace'));
        $namespace = str_replace("\\", '/', $namespace) . '/';
        $basePath = config('api-builder.requests.base');
        $base = Str::endsWith($basePath, '/') ? $basePath : $basePath . '/';
        return $basePath . $namespace . $this->argument('name') . '.php';
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
        $namespace = $this->getNamespace($name);
        $classname = $this->getClass($name);
        
        if(!$this->argument('name')){
            throw new InvalidArgumentException("Missing required argument request name");
        }

        //$rules = 'name=required|max:50;description=nullable';
        $rules = rtrim($this->option('rules'), ';');

        $stub = parent::replaceClass($stub, $classname);

        $stub = $this->setNamespace($stub, $namespace);

        $stub = $this->replaceClassName($stub, $classname);

        $stub = $this->replaceValidationFields($stub, $this->getRules($rules));

        return $stub;
    }

    protected function getNamespace($name)
    {
        $name = str_replace("App\\", '', $name);

        $nameArray = explode("\\", $name);

        $classname = array_pop($nameArray);

        return config('api-builder.requests.namespace') . (count($nameArray) > 0  ? "\\" . implode("\\", $nameArray) : '');
    }

    protected function getClass($name)
    {
        $nameArray = explode("\\", $name);

        return end($nameArray);
    }

    protected function setNamespace($stub, $namespace)
    {
        return str_replace('DummyNamespace', $namespace, $stub);
    }

    protected function replaceClassName($stub, $name)
    {
        return str_replace('DummyRequest', $name, $stub);
    }

    protected function replaceValidationFields($stub, $fields)
    {
        return str_replace('{{validationFields}}', $fields, $stub);
    }

    protected function getRules($fields)
    {
        $rules = '';
        if (trim($fields) != '') {

            $fields = explode(';', $fields);
            foreach ($fields as $field) {
                if (trim($field) == '') {
                    continue;
                }

                list($fieldName, $rule) = explode('=', $field);

                $rules .= "\n\t\t\t'$fieldName' => '$rule',";
            }
        }

        return $rules;
    }
}