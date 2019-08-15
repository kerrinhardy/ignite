<?php

namespace Kerrinhardy\Ignite;

use Illuminate\Support\ServiceProvider;
use Kerrinhardy\Ignite\IgniteModelCommand;

class IgniteServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                IgniteModelCommand::class,
                IgniteMigrationCommand::class
            ]);
        }
    }
}
