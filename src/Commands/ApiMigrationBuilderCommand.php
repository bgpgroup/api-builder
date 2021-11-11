<?php

namespace BgpGroup\ApiBuilder\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Exception\InvalidArgumentException;

class ApiMigrationBuilderCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bgp:make:migration 
                            {name} : The name of the Migration
                            {--columns= : The list of validations.}
                            ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Migration';

    protected $type = 'Migration';

    /**
     * 
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/../stubs/' . '/' . 'migration.stub';
    }

    protected function getPath($name)
    {
        $name = str_replace($this->laravel->getNamespace(), '', $name);
        $name = str_replace('-', '_', $name);
        return database_path('/migrations/') . date('Y_m_d_His') . '_create_' . $name . '_table.php';
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

        //$columns = 'string:name,50;text:description|nullable';
        $columns = rtrim($this->option('columns'), ';');

        $stub = parent::replaceClass($stub, $name);

        $stub = $this->replaceClassName($stub, $this->argument('name'));
        $stub = $this->replaceTableName($stub, $this->argument('name'));

        $stub = $this->replaceColumns($stub, $this->getColumns($columns));

        return $stub;
    }

    protected function replaceClassName($stub, $name)
    {
        $name = Str::studly($name);
        return str_replace('Dummy', $name, $stub);
    }

    protected function replaceTableName($stub, $name)
    {
        return str_replace('{{tableName}}', $name, $stub);
    }

    protected function replaceColumns($stub, $columns)
    {
        return str_replace('{{columns}}', $columns, $stub);
    }

    protected function getColumns($columns)
    {
        $result = '';

        if (trim($columns) != '') {

            $columns = explode(';', $columns);
            foreach ($columns as $column) {
                if (trim($column) == '') {
                    continue;
                }

                $types = explode('|', $column);
                $result .= "\n\t\t\t\$table";
                foreach ($types as $type) {
                    $methods = explode(':', $type);
                    $result .= "->" . $methods[0] . "("; // "('name', 50)";
                    if (!isset($methods[1])) {
                        $result .= ")";
                        continue;
                    }
                    $values = explode(',', $methods[1]);
                    // verify type enum (to do)
                    $result .= "'" . $values[0] . "'";
                    if (isset($values[1])) {
                        $result .= ", " . $values[1];
                    }
                    if (isset($values[2])) {
                        $result .= ", " . $values[2];
                    }
                    $result .= ")";
                }
                $result .= ";";
            }
        }

        return $result;
    }
}