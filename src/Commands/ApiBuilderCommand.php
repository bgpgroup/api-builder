<?php

namespace BgpGroup\ApiBuilder\Commands;

use App\Models\User;
use App\Support\DripEmailer;
use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Exception\InvalidArgumentException;

class ApiBuilderCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bgp:make:model {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate api model';

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
        return __DIR__.'/../stubs/' . '/' . 'Model.stub';
    }

    protected function getDefaultNamespace($rootnamespace)
    {
        return $rootnamespace . '\Models';
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

        $stub = parent::replaceClass($stub, $name);

        $stub = str_replace('DummyModel', $this->argument('name'), $stub);

        $stub = str_replace('DummyExtendsPath', config('api-builder.models.extends'), $stub);

        $extendsArray = explode('\\', config('api-builder.models.extends'));
        $extendsClass = end($extendsArray);

        return str_replace('DummyExtendsName', $extendsClass, $stub);

    }
}