<?php

namespace BgpGroup\ApiBuilder\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use File;

class ResourceBuilderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bgp:make:resource
                            {name} : The name of the resource
                            {--module= : The module name}
                            ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Module';

    public function handle()
    {
        $params = ['name' => $this->argument('name'), '--module' => $this->option('module')];

        $this->call('bgp:make:migration', $params);
        $this->call('bgp:make:model', $params);
        $this->call('bgp:make:factory', $params);
        $this->call('bgp:make:request', $params);
        $this->call('bgp:make:dto', $params);
        $this->call('bgp:make:collection', $params);
        $this->call('bgp:make:controller', $params);
        $this->call('bgp:make:policy', $params);
        $this->call('bgp:make:test', $params);

        $this->addPolicies();
        $this->addRoutes();
    }

    protected function addPolicies()
    {
        $authProviderFile = 'src/Modules/' . Str::studly($this->option('module')) . '/Providers/AuthServiceProvider.php';
        $content = file_get_contents(base_path($authProviderFile));
        $search = '    ];';

        $replace = '' .
            "        " . Str::studly($this->argument('name')) . "::class => " . Str::studly($this->argument('name')) . "Policy::class," .
            "\n    ];";
        
        $content = str_replace($search, $replace, $content);
        file_put_contents(base_path($authProviderFile), $content);

        $search = 'use App\Providers\AuthServiceProvider as ServiceProvider;';
        $replace = "use App\Providers\AuthServiceProvider as ServiceProvider;" .
            "\nuse Modules\\" . Str::studly($this->option('module')) . '\Models\\' . Str::studly($this->argument('name')) .';' .
            "\nuse Modules\\" . Str::studly($this->option('module')) . '\Policies\\' . Str::studly($this->argument('name')) . 'Policy;';
            
        $content = str_replace($search, $replace, $content);
        file_put_contents(base_path($authProviderFile), $content);
    }

    protected function addRoutes()
    {
        $authProviderFile = 'src/Modules/' . Str::studly($this->option('module')) . '/routes/api.php';
        $content = file_get_contents(base_path($authProviderFile));
        $search = '});';

        $replace = '' .
        "    Route::apiResource('" . Str::plural(Str::lower(Str::snake($this->argument('name'), '-'))) . "', Control\\" . Str::studly($this->argument('name')) . "Controller::class);" .
        "\n});";
        
        $content = str_replace($search, $replace, $content);
        file_put_contents(base_path($authProviderFile), $content);
    }
}
