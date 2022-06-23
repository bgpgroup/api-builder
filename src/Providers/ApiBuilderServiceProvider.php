<?php

namespace BgpGroup\ApiBuilder\Providers;

use Illuminate\Support\ServiceProvider;
use BgpGroup\ApiBuilder\Commands\ModuleBuilderCommand;
use BgpGroup\ApiBuilder\Commands\ModelBuilderCommand;
use BgpGroup\ApiBuilder\Commands\RequestBuilderCommand;
use BgpGroup\ApiBuilder\Commands\ControllerBuilderCommand;
use BgpGroup\ApiBuilder\Commands\ApiCrudBuilderCommand;
use BgpGroup\ApiBuilder\Commands\MigrationBuilderCommand;
use BgpGroup\ApiBuilder\Commands\TestBuilderCommand;
use BgpGroup\ApiBuilder\Commands\FactoryBuilderCommand;
use BgpGroup\ApiBuilder\Commands\DTObjectBuilderCommand;
use BgpGroup\ApiBuilder\Commands\CollectionBuilderCommand;
use BgpGroup\ApiBuilder\Commands\AppProviderBuilderCommand;
use BgpGroup\ApiBuilder\Commands\AuthProviderBuilderCommand;
use BgpGroup\ApiBuilder\Commands\InitRouterBuilderCommand;
use BgpGroup\ApiBuilder\Commands\ConfigBuilderCommand;

class ApiBuilderServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../../config/api-builder.php' => config_path('api-builder.php'),
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands([
                ModuleBuilderCommand::class,
                ModelBuilderCommand::class,
                RequestBuilderCommand::class,
                ApiCrudBuilderCommand::class,
                MigrationBuilderCommand::class,
                TestBuilderCommand::class,
                FactoryBuilderCommand::class,
                DTObjectBuilderCommand::class,
                CollectionBuilderCommand::class,
                ControllerBuilderCommand::class,
                AppProviderBuilderCommand::class,
                AuthProviderBuilderCommand::class,
                InitRouterBuilderCommand::class,
                ConfigBuilderCommand::class,
            ]);
        }
    }
}
