<?php

namespace Muzidudu\LaravelRecord;

use Illuminate\Support\ServiceProvider;

class RecordServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            \dirname(__DIR__) . '/config/record.php' => config_path('record.php'),
        ], 'record-config');

        $this->publishes([
            \dirname(__DIR__) . '/migrations/' => database_path('migrations'),
        ], 'record-migrations');

        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(\dirname(__DIR__) . '/migrations/');
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(
            \dirname(__DIR__) . '/config/record.php',
            'record'
        );
    }
}
