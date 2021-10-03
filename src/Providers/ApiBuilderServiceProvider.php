<?php

namespace BgpGroup\ApiBuilder\Providers;

use Illuminate\Support\ServiceProvider;
use BgpGroup\ApiBuilder\Commands\ApiBuilderCommand;

class ApiBuilderServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/api-builder.php' => config_path('api-builder.php'),
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands([
                ApiBuilderCommand::class,
            ]);
        }
    }
}