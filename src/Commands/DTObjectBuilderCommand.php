<?php

namespace BgpGroup\ApiBuilder\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Exception\InvalidArgumentException;

class DTObjectBuilderCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bgp:make:dto
                            {name} : The name of the resource
                            {--module= : The moule name}
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

    protected function fields()
    {
        return config(Str::lower($this->option('module')) . '.resources.' . Str::studly($this->argument('name')) . '.dto.fields');
    }

    protected function getPath($name)
    {
        return 'src/Modules/' . Str::studly($this->option('module')) . '/DTObjects/' . Str::studly($this->argument('name')) . 'DTObject.php';
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

        $stub = str_replace('$DECLARATION_FIELDS$', $this->getDeclarationFields(), $stub);

        $stub = str_replace('$MODEL_FIELDS$', $this->getModelFields(), $stub);

        return str_replace('$ARRAY_FIELDS$', $this->getArrayFields(), $stub);
    }

    protected function getDeclarationFields()
    {
        $fields = $this->fields();

        $result = '';

        foreach ($fields as $field) {
            $result .= "\n        public ?string \$$field,";
        }

        return $result;
    }

    protected function getModelFields()
    {
        $fields = $this->fields();

        $result = '';

        foreach ($fields as $field) {
            $result .= "\n            $field: \$model->$field,";
        }

        return $result;
    }

    protected function getArrayFields()
    {
        $fields = $this->fields();

        $result = '';

        foreach ($fields as $field) {
            $result .= "\n            $field: \$data['$field'],";
        }

        return $result;
    }
}
