<?php

namespace BgpGroup\ApiBuilder\Providers;

use Illuminate\Support\ServiceProvider;
use BgpGroup\ApiBuilder\Commands\ModuleBuilderCommand;
use BgpGroup\ApiBuilder\Commands\ApiModelBuilderCommand;
use BgpGroup\ApiBuilder\Commands\ApiRequestBuilderCommand;
use BgpGroup\ApiBuilder\Commands\ApiCollectionBuilderCommand;
use BgpGroup\ApiBuilder\Commands\ControllerBuilderCommand;
use BgpGroup\ApiBuilder\Commands\ApiCrudBuilderCommand;
use BgpGroup\ApiBuilder\Commands\ApiMigrationBuilderCommand;
use BgpGroup\ApiBuilder\Commands\ApiTestBuilderCommand;
use BgpGroup\ApiBuilder\Commands\ApiFactoryBuilderCommand;
use BgpGroup\ApiBuilder\Commands\ApiDTObjectBuilderCommand;
use BgpGroup\ApiBuilder\Commands\CollectionBuilderCommand;
use BgpGroup\ApiBuilder\Commands\AppProviderBuilderCommand;
use BgpGroup\ApiBuilder\Commands\AuthProviderBuilderCommand;
use BgpGroup\ApiBuilder\Commands\ApiRouterBuilderCommand;
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
                ApiModelBuilderCommand::class,
                ApiRequestBuilderCommand::class,
                ApiCollectionBuilderCommand::class,
                ApiCrudBuilderCommand::class,
                ApiMigrationBuilderCommand::class,
                ApiTestBuilderCommand::class,
                ApiFactoryBuilderCommand::class,
                ApiDTObjectBuilderCommand::class,
                CollectionBuilderCommand::class,
                ControllerBuilderCommand::class,
                AppProviderBuilderCommand::class,
                AuthProviderBuilderCommand::class,
                ApiRouterBuilderCommand::class,
                ConfigBuilderCommand::class,
            ]);
        }
    }
}