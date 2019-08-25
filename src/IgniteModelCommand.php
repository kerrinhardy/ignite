<?php

namespace Kerrinhardy\Ignite;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use File;

class IgniteModelCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ignite:model {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create CRUD operations including Model, Migration, Controller and Request.';

    /**
     * Get the relevant stub file for the command.
     */
    protected function getStub($type)
    {
        return file_get_contents(__DIR__ . '/resources/stubs/' . $type . '.stub');
    }

    /**
     * Create a new Model from the stub and the name entered in the command.
     *
     * @var string
     */
    protected function model($name)
    {
        $modelTemplate = str_replace(
            ['{{modelName}}'],
            [$name],
            $this->getStub('Model')
        );

        file_put_contents(app_path("/{$name}.php"), $modelTemplate);
    }

    /**
     * Create a new Controller from the stub and the name entered in the command.
     *
     * @var string
     */
    protected function controller($name)
    {
        $controllerTemplate = str_replace(
            [
                '{{modelName}}',
                '{{modelNamePluralLowerCase}}',
                '{{modelNameSingularLowerCase}}'
            ],
            [
                $name,
                strtolower(Str::plural($name)),
                strtolower($name)
            ],
            $this->getStub('Controller')
        );

        $directoryPath = app_path("/Http/Controllers/API/");

        if (!File::exists($directoryPath)) {
            File::makeDirectory($directoryPath, 0770, true);
        }

        file_put_contents($directoryPath . $name . 'Controller.php', $controllerTemplate);
    }

    /**
     * Create a new Request from the stub and the name entered in the command.
     *
     * @var string
     */
    protected function request($name)
    {
        $requestTemplate = str_replace(
            ['{{modelName}}'],
            [$name],
            $this->getStub('Request')
        );

        if (!file_exists($path = app_path('/Http/Requests'))) {
            mkdir($path, 0770, true);
        }

        file_put_contents(app_path("/Http/Requests/{$name}Request.php"), $requestTemplate);
    }

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $name = $this->argument('name');

        $this->controller($name);
        $this->info('API Controller for ' . $name . ' created successfully.');

        $this->model($name);
        $this->info('Model for ' . $name . ' created successfully.');

        $this->request($name);
        $this->info('Request for ' . $name . ' created successfully.');

        File::append(base_path('routes/api.php'),
            PHP_EOL . 'Route::resource(\'' . strtolower(Str::plural($name)) . "', '{$name}Controller');");

        $this->info('Added to API routes file.');

        File::append(base_path('routes/web.php'),
            PHP_EOL . 'Route::resource(\'' . strtolower(Str::plural($name)) . "', '{$name}Controller');");

        $this->info('Added to Web routes file.');
    }
}
