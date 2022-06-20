<?php

namespace BgpGroup\ApiBuilder\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use File;

class ModuleBuilderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bgp:make:module
                            {name} : The name of the module
                            ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Module';

    public function handle()
    {
        $path = base_path('src/Modules/' . $this->argument('name'));

        if(!File::isDirectory($path)) {
            File::makeDirectory($path);
        }

        File::makeDirectory($path . '/Tests');
        File::makeDirectory($path . '/Tests/Feature');
        $this->updatePhpunitXml();

        $this->call('bgp:make:config', ['name' => $this->argument('name')]);
        $this->call('bgp:make:api-router', ['name' => $this->argument('name')]);
        $this->call('bgp:make:app-provider', ['name' => $this->argument('name')]);
        $this->call('bgp:make:auth-provider', ['name' => $this->argument('name')]);

        $this->addedProvidersToCofig();
    }

    protected function updatePhpunitXml()
    {
        $content = file_get_contents(base_path('phpunit.xml'));
        $search = '<testsuite name="Feature">';
        $replace = '<testsuite name="Feature">' . "\n\t\t\t" . '<directory suffix="Test.php">./src/Modules/' . Str::studly($this->argument('name')) . '/Tests/Feature</directory>';
        $content = str_replace($search, $replace, $content);
        file_put_contents(base_path('phpunit.xml'), $content);
    }

    protected function addedProvidersToCofig()
    {
        $content = file_get_contents(base_path('config/app.php'));
        $search = '// BGP';
        $replace = '// BGP' . "\n\t\t" . 'Modules\Locations\Providers\AppServiceProvider::class,' . "\n\t\t" . 'Modules\Locations\Providers\AuthServiceProvider::class,';
        $content = str_replace($search, $replace, $content);
        file_put_contents(base_path('config/app.php'), $content);
    }
}
