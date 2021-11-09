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
                            {--group= : The group.}
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
        return database_path('/factories/') . $this->option('group') . '/' . $name . '.php';
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
        //$classname = $this->getClass($name);

        if(!$this->argument('name')){
            throw new InvalidArgumentException("Missing required argument request name");
        }

        //$columns = 'string:name;text:description;foreign:team_id';
        $columns = rtrim($this->option('columns'), ';');

        $stub = $this->setNamespace($stub, $namespace);
        $stub = $this->replaceModelClass($stub, $name);
        $stub = parent::replaceClass($stub, $name);

        $stub = $this->replaceClassName($stub, $name);

        $stub = $this->replaceColumns($stub, $this->getColumns($columns));

        return $stub;
    }

    protected function getNamespace($name)
    {
        return $this->getDomainPath();
    }

    protected function getDomainPath($type = '')
    {
        $group = str_replace("/", "\\", $this->option('group')) ;

        return 'Database\Factories' . "\\" . $group .  (!empty($type) ? ("\\" . $type) : '');
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

                $column = explode('|', $column)[0];
                $methods = explode(':', $column);

                $params = explode(',', $methods[1])[0];

                if ($methods[0] == 'foreign' || $methods[0] == 'foreignId') {
                    $class = Str::studly(str_replace('_id', '', $params));
                    $result .= "\n\t\t\t'" . $params ."' => " . $class . "::factory()->create()->id,";
                    continue;
                }

                $result .= "\n\t\t\t'" . $params ."' => " . $typeValues[$methods[0]] . ",";
            }
        }

        return $result;
    }

    protected function setNamespace($stub, $namespace)
    {
        return str_replace('DummyNamespace', $namespace, $stub);
    }

    protected function replaceModelClass($stub, $name)
    {
        $name = str_replace('Factory', '', $name);
        
        $group = str_replace("/", "\\", $this->option('group')) ;

        $name = 'Domain' . "\\" . $group .  "\\" . "Models" . "\\" . $name;

        $name = str_replace("/", "\\", $name);

        return str_replace('DummyModelUse', $name, $stub);
    }

}