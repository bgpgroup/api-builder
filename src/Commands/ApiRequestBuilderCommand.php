<?php

namespace BgpGroup\ApiBuilder\Commands;

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

     /**
     * Execute the console command.
     *
     * @return bool|null
     */
    public function fire(){

        $this->setModelClass();
    }

    /**
     * 
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/../stubs/' . '/' . 'Request.stub';
    }

    protected function getDefaultNamespace($rootnamespace)
    {
        return $rootnamespace . '\Http\Requests';
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
            throw new InvalidArgumentException("Missing required argument model name");
        }

        //$fields = 'name=required|max:50;description=nullable';
        $fields = rtrim($this->option('rules'), ';');

        $stub = parent::replaceClass($stub, $name);

        $stub = $this->replaceClassName($stub, $this->argument('name'));

        $stub = $this->replaceValidationFields($stub, $this->getRules($fields));

        return $stub;
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