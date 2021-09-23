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
            [
                '{{modelName}}',
                '{{modelNameSingularLowerCase}}'
            ],
            [
                $name,
                strtolower($name)
            ],
            $this->getStub('Model')
        );

        file_put_contents(app_path("/{$name}.php"), $modelTemplate);
    }

    /**
     * Get the full path to the factory.
     *
     * @param string $name
     * @return string
     */
    protected function getFactoryPath($name)
    {
        return base_path() . '/database/factories/' . $name . 'Factory.php';
    }

    /**
     * Create a new Factory from the stub and the names entered in the command.
     *
     * @var string
     * @var array
     */
    protected function factory($name)
    {
        $factoryTemplate = str_replace(
            [
                '{{modelName}}'
            ],
            [
                $name
            ],
            $this->getStub('Factory')
        );

        file_put_contents($this->getFactoryPath($name), $factoryTemplate);
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

        $directoryPath = app_path("/Http/Controllers/");

        if (!File::exists($directoryPath)) {
            File::makeDirectory($directoryPath, 0770, true);
        }

        file_put_contents($directoryPath . $name . 'Controller.php', $controllerTemplate);
    }

    /**
     * Create a new API Controller from the stub and the name entered in the command.
     *
     * @var string
     */
    protected function controllerApi($name)
    {
        $controllerApiTemplate = str_replace(
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
            $this->getStub('ControllerApi')
        );

        $directoryPath = app_path("/Http/Controllers/API/");

        if (!File::exists($directoryPath)) {
            File::makeDirectory($directoryPath, 0770, true);
        }

        file_put_contents($directoryPath . $name . 'ApiController.php', $controllerApiTemplate);
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

        file_put_contents(app_path("/Http/Requests/{$name}FormRequest.php"), $requestTemplate);
    }

    /**
     * Create a new Policy from the stub and the name entered in the command.
     *
     * @var string
     */
    protected function policy($name)
    {
        $policyTemplate = str_replace(
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
            $this->getStub('Policy')
        );

        if (!file_exists($path = app_path('/Policies'))) {
            mkdir($path, 0770, true);
        }

        file_put_contents(app_path("/Policies/{$name}Policy.php"), $policyTemplate);
    }

    /**
     * Create a new Feature Test from the stub and the name entered in the command.
     *
     * @var string
     */
    protected function test_feature($name)
    {
        $testFeatureTemplate = str_replace(
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
            $this->getStub('TestFeature')
        );

        if (!file_exists($path = app_path('/Tests/Feature'))) {
            mkdir($path, 0770, true);
        }

        file_put_contents(app_path("/Tests/Feature/Manage{".Str::plural($name)."}Test.php"), $testFeatureTemplate);
    }

    /**
     * Create a new Unit Test from the stub and the name entered in the command.
     *
     * @var string
     */
    protected function test_unit($name)
    {
        $testUnitTemplate = str_replace(
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
            $this->getStub('TestFeature')
        );

        if (!file_exists($path = app_path('/Tests/Unit'))) {
            mkdir($path, 0770, true);
        }

        file_put_contents(app_path("/Tests/Unit/{$name}Test.php"), $testUnitTemplate);
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
        $this->info('Controller for ' . $name . ' created successfully.');

//        $this->controllerApi($name);
//        $this->info('API Controller for ' . $name . ' created successfully.');

        $this->model($name);
        $this->info('Model for ' . $name . ' created successfully.');

        $this->request($name);
        $this->info('Request for ' . $name . ' created successfully.');

        $this->policy($name);
        $this->info('Policy for ' . $name . ' created successfully.');

        $this->factory($name);
        $this->info('Factory for ' . $name . ' created successfully.');

        $this->test_feature($name);
        $this->info('Feature Test for ' . $name . ' created successfully.');

        $this->test_unit($name);
        $this->info('Unit Test for ' . $name . ' created successfully.');

//        File::append(base_path('routes/api.php'),
//            PHP_EOL . 'Route::resource(\'' . strtolower(Str::plural($name)) . "', '{$name}Controller');");
//
//        $this->info('Added to API routes file.');

        File::append(base_path('routes/web.php'),
            PHP_EOL . 'Route::resource(\'' . strtolower(Str::plural($name)) . "', '{$name}Controller');");

        $this->info('Added to Web routes file.');
    }
}
