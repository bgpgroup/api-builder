<?php

namespace BgpGroup\ApiBuilder\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Exception\InvalidArgumentException;

class MigrationBuilderCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bgp:make:migration
                            {name} : The name of the module
                            {--module= : The moule name}
                            ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate migration';

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

    protected function tableName()
    {
        return config(Str::lower($this->option('module')) . '.resources.' . Str::studly($this->argument('name')) . '.migration.table');
    }

    protected function tableColumns()
    {
        return config(Str::lower($this->option('module')) . '.resources.' . Str::studly($this->argument('name')) . '.migration.columns');
    }

    protected function getPath($name)
    {
        return 'src/Modules/' . Str::studly($this->option('module')) . '/migrations/' . date('Y_m_d_His') . '_create_' . $this->tableName() . '_table.php';
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

        return str_replace('$COLUMNS_BLOCK$', $this->getColumns(), $stub);
    }

    protected function getColumns()
    {
        $columns = $this->tableColumns();

        $result = '';

        foreach ($columns as $name => $options) {

            if ($name == 'id' && $options) {
                $result .= "\n\t\t\t\$table->uuid('id')->primary();";
                continue;
            }

            if (($options['type'] ?? false)) {
                $result .= "\n\t\t\t\$table->" . $options['type'] . "('" . $name . "'";
                $result .= ($options['type'] == 'string' && ($options['size'] ?? false)) ? ", " . $options['size'] : "";
                $result .= ($options['type'] == 'enum' && $options['options'] ?? false) ? ", " . $options['options'] : "";
                $result .= ")";

                if (($options['nullable'] ?? false)) {
                    $result .= "->nullable()";
                }

                if (($options['default'] ?? false)) {
                    $result .= "->default('" . $options['default'] . "')";
                }

                $result .= ";";

                continue;
            }

            if ($name == 'active' && $options) {
                $result .= "\n\t\t\t\$table->enum('active',['on', 'off'])->default('on');";
                continue;
            }

            if ($name == 'created_by' && $options) {
                $result .= "\n\t\t\t\$table->foreignUuid('created_by')->nullable()->constrained('users');";
                continue;
            }

            if ($name == 'company_id' && $options) {
                $result .= "\n\t\t\t\$table->foreignUuid('company_id');";
                continue;
            }

            if ($name == 'timestamps' && $options) {
                $result .= "\n\t\t\t\$table->timestamps();";
                continue;
            }

            if ($name == 'soft_delete' && $options) {
                $result .= "\n\t\t\t\$table->softDeletes();";
                continue;
            }
        }

        return $result;
    }
}
