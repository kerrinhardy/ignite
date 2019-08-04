<?php

namespace kerrinhardy\ignite;

use Illuminate\Support\ServiceProvider;
use kerrinhardy\ignite\Commands\IgniteModelCommand;

class IgniteServiceProvider extends ServiceProvider
{
    protected $commands = [
        'Kerrinhardy\Ignite\Commands\IgniteModelCommand::Class',
    ];

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->commands($this->commands);
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
            ]);
        }
    }
}
