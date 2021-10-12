<?php

namespace BgpGroup\ApiBuilder\Providers;

use Illuminate\Support\ServiceProvider;
use BgpGroup\ApiBuilder\Commands\ApiModelBuilderCommand;
use BgpGroup\ApiBuilder\Commands\ApiRequestBuilderCommand;
use BgpGroup\ApiBuilder\Commands\ApiCollectionBuilderCommand;
use BgpGroup\ApiBuilder\Commands\ApiResourceBuilderCommand;
use BgpGroup\ApiBuilder\Commands\ApiControllerBuilderCommand;
use BgpGroup\ApiBuilder\Commands\ApiCrudBuilderCommand;
use BgpGroup\ApiBuilder\Commands\ApiMigrationBuilderCommand;
use BgpGroup\ApiBuilder\Commands\ApiTestBuilderCommand;
use BgpGroup\ApiBuilder\Commands\ApiFactoryBuilderCommand;

class ApiBuilderServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../../config/api-builder.php' => config_path('api-builder.php'),
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands([
                ApiModelBuilderCommand::class,
                ApiRequestBuilderCommand::class,
                ApiCollectionBuilderCommand::class,
                ApiResourceBuilderCommand::class,
                ApiControllerBuilderCommand::class,
                ApiCrudBuilderCommand::class,
                ApiMigrationBuilderCommand::class,
                ApiTestBuilderCommand::class,
                ApiFactoryBuilderCommand::class,
            ]);
        }
    }
}