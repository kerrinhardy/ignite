<?php

namespace Kerrinhardy\Ignite;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use File;

class IgniteMigrationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ignite:migration {name}';

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
     * Create a new Migration from the stub and the names entered in the command.
     *
     * @var string
     * @var array
     */
    protected function migration($name, $columns)
    {

        $columns = collect($columns);

        $migrations = [];

        foreach($columns as $name => $type) {
            if($type == 'String') {
                $migrations[] = '$table->string(\'' . $name . '\');';
            }
        }

        $migrationTemplate = str_replace(
            [
                '{{modelNamePlural}}',
                '{{modelNamePluralLowerCase}}',
                '{{columns}}'
            ],
            [
                Str::plural(Str::studly($name)),
                Str::plural(strtolower($name)),
                $migrations
            ],
            $this->getStub('Migration')
        );

        file_put_contents(getPath($name), $migrationTemplate);
    }

    /**
     * Get the date prefix for the migration.
     *
     * @return string
     */
    protected function getDatePrefix()
    {
        return date('Y_m_d_His');
    }

    /**
     * Get the full path to the migration.
     *
     * @param  string $name
     * @return string
     */
    protected function getPath($name)
    {
        return app_path() . '/database/migrations/' . $this->getDatePrefix() . '_' . $name . '.php';
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

        $col_name = [];
        $col_type = [];

        $i = 0;

        do {
            $i++;
            $col_name[$i] = $this->ask('Column name?');
            if($col_name[$i] != null) {
                $col_type[$i] = $this->choice('Column type?', ['Boolean', 'dateTimeTz', 'String'], 'String');
            }
        } while ($col_name[$i] != null);

        array_pop($col_name);

        $this->migration($name, $col_name);
        $this->info('Migration for ' . $name . ' created successfully.');

    }
}
