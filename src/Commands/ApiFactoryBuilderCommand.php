<?php

namespace BgpGroup\ApiBuilder\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Exception\InvalidArgumentException;

class ApiFactoryBuilderCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bgp:make:factory 
                            {name} : The name of the Factory
                            {--columns= : The list of validations.}
                            ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Factory';

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

    protected function getPath($name)
    {
        $name = str_replace($this->laravel->getNamespace(), '', $name);
        return database_path('/factories/') . $name . '.php';
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
            throw new InvalidArgumentException("Missing required argument request name");
        }

        //$columns = 'string:name;text:description;foreign:team_id';
        $columns = rtrim($this->option('columns'), ';');

        $stub = parent::replaceClass($stub, $name);

        $stub = $this->replaceClassName($stub, $this->argument('name'));

        $stub = $this->replaceColumns($stub, $this->getColumns($columns));

        return $stub;
    }

    protected function replaceClassName($stub, $name)
    {
        $name = str_replace('Factory', '', $name);
        return str_replace('Dummy', $name, $stub);
    }

    protected function replaceColumns($stub, $columns)
    {
        return str_replace('{{columns}}', $columns, $stub);
    }

    protected function getColumns($columns)
    {
        $result = '';

        $typeValues = [
            'string' => '$this->faker->word',
            'text' => '$this->faker->sentence',
        ];

        if (trim($columns) != '') {

            $columns = explode(';', $columns);

            foreach ($columns as $column) {

                if (trim($column) == '') {
                    continue;
                }

            //    'name' => $this->faker->word,
            //    'description' => $this->faker->sentence,
            //    'team_id' => Team::factory()->create()->id,

                $methods = explode(':', $column);

                if ($methods[0] == 'foreign' || $methods[0] == 'foreignId') {
                    $class = Str::studly(str_replace('_id', '', $methods[1]));
                    $result .= "\n\t\t\t'" . $methods[1] ."' => " . $class . "::factory()->create()->id,";
                    continue;
                }

                $result .= "\n\t\t\t'" . $methods[1] ."' => " . $typeValues[$methods[0]] . ",";
            }
        }

        return $result;
    }
}