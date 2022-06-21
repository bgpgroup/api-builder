<?php

namespace BgpGroup\ApiBuilder\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Exception\InvalidArgumentException;

class ModelBuilderCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bgp:make:model
                            {name} : The name of the module
                            {--module= : The moule name}
                            ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate model';

    protected $type = 'Model';

    /**
     *
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/../stubs/' . '/' . 'model.stub';
    }

    protected function tableName()
    {
        return config(Str::lower($this->option('module')) . '.resources.' . Str::studly($this->argument('name')) . '.model.table');
    }

    protected function fillable()
    {
        return config(Str::lower($this->option('module')) . '.resources.' . Str::studly($this->argument('name')) . '.model.fillable');
    }

    protected function searchable()
    {
        return config(Str::lower($this->option('module')) . '.resources.' . Str::studly($this->argument('name')) . '.model.searchable');
    }

    protected function getPath($name)
    {
        return 'src/Modules/' . Str::studly($this->option('module')) . '/Models/' . Str::studly($this->argument('name')) . '.php';
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

        $stub = str_replace('$TABLE_NAME$', $this->tableName(), $stub);

        $stub = str_replace('$CAMEL_MODULE_NAME$', Str::studly($this->option('module')), $stub);

        $stub = str_replace('$CAMEL_RESOURCE_NAME$', Str::studly($this->argument('name')), $stub);

        $stub = str_replace('$FILLABLE_FIELDS$', $this->getFilables(), $stub);

        return str_replace('$SEARCHABLE_FIELDS$', $this->getSearchable(), $stub);
    }

    protected function getFilables()
    {
        $fields = $this->fillable();

        $result = '';

        foreach ($fields as $field) {
            $result .= "\n        '$field',";
        }

        return $result;
    }

    protected function getSearchable()
    {
        $fields = $this->searchable();

        $result = '';

        foreach ($fields as $field) {
            $result .= "\n        '$field',";
        }

        return $result;
    }
}
