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
        return base_path() . '/database/migrations/' . $this->getDatePrefix() . '_create_' . Str::plural(strtolower($name)) . '_table.php';
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
     * Create a new Migration from the stub and the names entered in the command.
     *
     * @var string
     * @var array
     */
    protected function migration($name, $columns)
    {
        $columns = collect($columns);

        $migrations = '';

        foreach($columns as $column => $type) {
            if($type == 'string') {
                $migrations .= '$table->string(\'' . $column . '\');' . PHP_EOL;
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

        file_put_contents($this->getPath($name), $migrationTemplate);
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
                $col_type[$i] = $this->choice('Column type?', ['boolean', 'dateTimeTz', 'string'], 'String');
            }
        } while ($col_name[$i] != null);

        array_pop($col_name);

        $columns = array_combine($col_name, $col_type);

        $this->migration($name, $columns);

        $this->info('Migration for ' . Str::plural(strtolower($name)) . ' table created successfully.');

    }
}
