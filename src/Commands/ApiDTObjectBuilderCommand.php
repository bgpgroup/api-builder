<?php

namespace BgpGroup\ApiBuilder\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Exception\InvalidArgumentException;

class ApiDTObjectBuilderCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bgp:make:dto 
                            {name} : The name of the dto
                            {--fields= : The list of fields separated by comma.}
                            {--model= : The model name with namespace.}
                            ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate DTO';

    protected $type = 'DTO';

    /**
     * 
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/../stubs/' . '/' . 'dto.stub';
    }

    protected function getDefaultNamespace($rootnamespace)
    {
        return 'App';
    }

    protected function getPath($name)
    {
        $namespace = str_replace("App\\", '', config('api-builder.dto.namespace'));
        $namespace = str_replace("\\", '/', $namespace) . '/';
        $basePath = config('api-builder.dto.base');
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
            throw new InvalidArgumentException("Missing required argument dto name");
        }

        $fields = rtrim($this->option('fields'), ',');

        $stub = $this->setNamespace($stub, $namespace);

        $stub = $this->replaceModelClass($stub, $this->option('model'));

        $stub = $this->replaceDeclarationFields($stub, $this->getDeclarationFields($fields));

        $stub = $this->replaceArrayFields($stub, $this->getArrayFields($fields));

        $stub = $this->replaceModelFields($stub, $this->getModelFields($fields));

        return $this->replaceClassName($stub, $classname);
    }

    protected function getNamespace($name)
    {
        $name = str_replace("App\\", '', $name);

        $nameArray = explode("\\", $name);

        $classname = array_pop($nameArray);

        return config('api-builder.dto.namespace') . (count($nameArray) > 0  ? "\\" . implode("\\", $nameArray) : '');
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
        return str_replace('DummyClassName', $name, $stub);
    }

    protected function replaceModelClass($stub, $name)
    {
        $name = str_replace("/", "\\", $name);

        $stub = str_replace('DummyModelUse', $name, $stub);

        $nameArray = explode("\\", $name);

        $modelName = end($nameArray);

        return str_replace('DummyModelName', $modelName, $stub);
    }

    protected function getDeclarationFields($fields)
    {
        $result = '';
        if (trim($fields) != '') {

            $fields = explode(',', $fields);
            foreach ($fields as $field) {
                if (trim($field) == '') {
                    continue;
                }
                list($type, $name) = explode(':', $field);
                $result .= "\n\t\tpublic $type \$$name,";            }
        }

        return $result;
    }

    protected function getModelFields($fields)
    {
        $result = '';
        if (trim($fields) != '') {

            $fields = explode(',', $fields);
            foreach ($fields as $field) {
                if (trim($field) == '') {
                    continue;
                }
                list($type, $name) = explode(':', $field);
                $result .= "\n\t\t\t$name: \$model->$name,";
            }
        }

        return $result;
    }

    protected function getArrayFields($fields)
    {
        $result = '';
        if (trim($fields) != '') {

            $fields = explode(',', $fields);
            foreach ($fields as $field) {
                if (trim($field) == '') {
                    continue;
                }
                list($type, $name) = explode(':', $field);
                $result .= "\n\t\t\t$name: \$data['$name'],";
            }
        }

        return $result;
    }

    protected function replaceDeclarationFields($stub, $fields)
    {
        return str_replace('{{declaration_fields}}', $fields, $stub);
    }

    protected function replaceModelFields($stub, $fields)
    {
        return str_replace('{{model_fields}}', $fields, $stub);
    }

    protected function replaceArrayFields($stub, $fields)
    {
        return str_replace('{{array_fields}}', $fields, $stub);
    }

}