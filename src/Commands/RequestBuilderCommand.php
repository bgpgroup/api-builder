<?php

namespace BgpGroup\ApiBuilder\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Exception\InvalidArgumentException;

class RequestBuilderCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bgp:make:request
                            {name} : The name of the resource
                            {--module= : The moule name}
                            ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate request';

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

    protected function createRules()
    {
        return config(Str::lower($this->option('module')) . '.resources.' . Str::studly($this->argument('name')) . '.request.rules.create');
    }

    protected function updateRules()
    {
        return config(Str::lower($this->option('module')) . '.resources.' . Str::studly($this->argument('name')) . '.request.rules.update');
    }

    protected function messages()
    {
        return config(Str::lower($this->option('module')) . '.resources.' . Str::studly($this->argument('name')) . '.request.messages');
    }

    protected function getPath($name)
    {
        return 'src/Modules/' . Str::studly($this->option('module')) . '/Requests/' . Str::studly($this->argument('name')) . 'Request.php';
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

        $stub = str_replace('$CREATE_RULES$', $this->getCreateRules(), $stub);

        $stub = str_replace('$UPDATE_RULES$', $this->getUpdateRules(), $stub);

        return str_replace('$MESSAGES$', $this->getMessages(), $stub);
    }

    protected function getCreateRules()
    {
        $rules = $this->createRules();

        $result = '';

        foreach ($rules as $name => $rule) {
            $result .= "\n                    '$name' => '$rule',";
        }

        return $result;
    }

    protected function getUpdateRules()
    {
        $rules = $this->updateRules();

        $result = '';

        foreach ($rules as $name => $rule) {
            $result .= "\n                    '$name' => '$rule',";
        }

        return $result;
    }

    protected function getMessages()
    {
        $messages = $this->messages();

        $result = '';

        foreach ($messages as $name => $message) {
            $result .= "\n            '$name' => '$message',";
        }

        return $result;
    }
}
